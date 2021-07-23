<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\User as UserModel;
use App\Service;

class User extends Component
{
    
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public $query;
    public $perPage = 10;
    public $updateMode = false;
    protected $rules = [
        'name' => 'required|string',
        'email' => 'sometimes|nullable|email',
        'code' => 'sometimes|nullable|alpha_num',
        'service_id' => 'required|int',
    ];

    //Table columns
    public  $name, $code, $email, $service_id, $selected_id, $confirming;

    public function render()
    {
        return view('livewire.users.component', [
            'data' => UserModel::has("service")->where('name', 'like', '%'.$this->query.'%')->paginate($this->perPage),
            'services' => $this->getAllServices()
        ]);
    }

    private function resetInput()
    {
        $this->name = null;
        $this->email = null;
        $this->code = null;
        $this->service_id = null;
    }

    public function store()
    {
        $this->validate();

        UserModel::create([
            'name' => $this->name,
            'code' => $this->code,
            'email' => $this->email,
            'service_id' => $this->service_id,
        ]);

        $this->resetInput();
        $this->updateMode = false;
    }

    public function edit($id)
    {
        $record = UserModel::findOrFail($id);

        $this->selected_id = $id;

        $this->name = $record->name;
        $this->code = $record->code;
        $this->email = $record->email;
        $this->service_id = $record->service_id;
        
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate();

        if ($this->selected_id) {
            $record = UserModel::find($this->selected_id);

            $record->update([
                'name' => $this->name,
                'code' => $this->code,
                'email' => $this->email,
                'service_id' => $this->service_id,
            ]);

            $this->resetInput();
            $this->updateMode = false;
        }

    }

    //Get All services

    public function getAllServices()
    {
        return Service::get();
    }

    //BEFORE DELETING
    public function confirmDelete($id)
    {
        $this->confirming = $id;
    }

    public function kill($id)
    {
        $this->destroy($id);
    }

    public function cancelKill($id)
    {
        $this->confirming = null;
    }

    public function destroy($id)
    {
        if ($id) {
            $record = UserModel::where('id', $id);
            $record->delete();
        }
    }

}

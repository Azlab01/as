<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use App\Conge as CongeModel;
use Livewire\Component;
use App\User;
use App\Service;

class Conge extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $query;
    public $updateMode = false;
    public $users =  [];
    public $perPage = 5;
    protected $rules = [
        'date_start_conge' => 'required|date',
        'date_end_conge' => 'required|date',
        'user_id' => 'required|array',
    ];

    //Table columns
    public $date_start_conge, $date_end_conge, $selected_id, $service_id, $confirming, $user_id = [];
    //Table search
    public $s_date_start_conge, $s_date_end_conge;
    //Variable Search
    public $searched = false;
    //Input range
    public $date_range = "";
    

    public function mount()
    {
        $this->getUsersHasConge();
    }

    public function render()
    {
        $datas = ($this->searched) ? $this->getUsersHasConge(true) : $this->getUsersHasConge();
        return view('livewire.Conges.component', [
                    'datas'=> $datas,
                    'services'=> $this->getAllServices()]);
    }

    public function updatedServiceId($value)
    {
        $this->users = User::where('service_id', $value)->get();
    }

    public function resetInput()
    {
        $this->date_start_conge = null;
        $this->date_end_conge = null;
        $this->user_id = null;
        $this->users = [];
        $this->service_id = null;
        $this->date_range = " ";
    }

    public function store()
    {
        $this->validate();
        $res = CongeModel::create([
            'date_start_conge' => $this->date_start_conge,
            'date_end_conge' => $this->date_end_conge,
        ]);

        if ($res->save()) {
            $res->users()->attach($this->user_id);
        }

        $this->getUsersHasConge();

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = CongeModel::findOrFail($id);

        $this->selected_id = $id;
        $this->date_start_conge = $record->date_start_conge->format('Y-m-d');
        $this->date_end_conge = $record->date_end_conge->format('Y-m-d');

        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'selected_id' => 'required|numeric',
            'date_start_conge' => 'required|date',
            'date_end_conge' => 'required|date'
        ]);

        if ($this->selected_id) {
            $record = CongeModel::find($this->selected_id);

            $record->update([
                'date_start_conge' => $this->date_start_conge,
                'date_end_conge' => $this->date_end_conge,
            ]);

            $record->users()->sync($this->user_id);
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
            $record = CongeModel::where('id', $id)->first();
            $record->users()->detach();
            $record->delete();
            $this->updateMode = false;
            $this->getUsersHasConge();
        }
    }

    public function getUsersHasConge($search = false)
    {
        if ($search && $this->s_date_start !== '' && $this->s_date_end) {
            $this->searched = true;
            return CongeModel::has('users')
                                        ->where('date_start', '>=', $this->s_date_start)
                                        ->where('date_end', '<=', $this->s_date_end)
                                        ->paginate($this->perPage);
        }else {          
            $this->searched = false;
            return CongeModel::has('users')->paginate($this->perPage);
        }
    }
}

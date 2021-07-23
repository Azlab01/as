<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Service as ServiceModel;

class Service extends Component
{
    
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';

    public $query;
    public $perPage = 10;
    public $updateMode = false;
    protected $rules = [
        'name' => 'required|string',
        'description' => 'sometimes|nullable|string',
    ];

    //Table columns
    public  $name, $description, $selected_id, $confirming;

    public function render()
    {
        return view('livewire.services.component', [
            'data' => ServiceModel::where('name', 'like', '%'.$this->query.'%')->paginate($this->perPage)
        ]);
    }

    private function resetInput()
    {
        $this->name = null;
        $this->description = null;
    }

    public function store()
    {
        $this->validate();

        ServiceModel::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->resetInput();
        $this->updateMode = false;
    }

    public function edit($id)
    {
        $record = ServiceModel::findOrFail($id);

        $this->selected_id = $id;

        $this->name = $record->name;
        $this->description = $record->description;
        
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate();

        if ($this->selected_id) {
            $record = ServiceModel::find($this->selected_id);

            $record->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);

            $this->resetInput();
            $this->updateMode = false;
        }

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
        $record = ServiceModel::where('id', $id);
        $record->users()->detach();
        $record->delete();
    }

}

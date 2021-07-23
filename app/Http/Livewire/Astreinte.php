<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use App\Astreinte as AstreinteModel;
use Livewire\Component;
use App\User;
use App\Service;

class Astreinte extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $query;
    public $updateMode = false;
    public $test = [];
    public $users =  [];
    public $perPage = 5;
    protected $rules = [
        'date_start' => 'required|date',
        'date_end' => 'required|date',
        'heure_start' => 'required|date_format:H:i',
        'heure_end' => 'required|date_format:H:i',
        'description' => 'sometimes|nullable|string',
        'user_id' => 'required|array',
    ];

    //Table columns
    public $date_start, $date_end, $heure_start, $heure_end, $selected_id, $description, $service_id, $confirming, $user_id = [];
    //Table search
    public $s_date_start, $s_date_end;
    //Variable Search
    public $searched = false;
    //Input range
    public $date_range = "";

    public function mount()
    {
        $this->getUsersHasAstreinte();
    }

    public function render()
    {
        

        $datas = ($this->searched) ? $this->getUsersHasAstreinte(true) : $this->getUsersHasAstreinte();
        return view('livewire.astreintes.component', [
                    "datas"=> $datas,
                    "services"=> $this->getAllServices()]);
    }

    public function updatedServiceId($value)
    {
        $this->users = User::where("service_id", $value)->get();
    }

    public function resetInput()
    {
        $this->date_start = null;
        $this->date_end = null;
        $this->heure_start = null;
        $this->heure_end = null;
        $this->description = null;
        $this->user_id = null;
        $this->users = [];
        $this->service_id = null;
        $this->date_range = " ";
    }

    public function store()
    {
        $this->validate();

        
        $dateDebut = date_create("$this->date_start $this->heure_start");
        $dateFin = date_create("$this->date_start $this->heure_end");

        $h1 = new \DateTime("$this->heure_start");
        $h2 = new \DateTime("$this->heure_end");
        
        $debut = $dateDebut->format('U');
        $fin = $dateFin->format('U');
       
        $heures = round(($fin - $debut) / 3600, 2);

        $res = AstreinteModel::create([
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'heure_start' => $this->heure_start,
            'heure_end' => $this->heure_end,
            'description' => $this->description,
            'nbr_hours' => $heures
        ]);

        $res->users()->attach($this->user_id);
        $this->getUsersHasAstreinte();

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = AstreinteModel::findOrFail($id);

        $this->selected_id = $id;
        $this->date_start = $record->date_start->format('Y-m-d');
        $this->date_end = $record->date_end->format('Y-m-d');
        $this->heure_start = $record->heure_start;
        $this->heure_end = $record->heure_end;

        $this->test = User::has('astreintes')->pluck("id")->toArray();
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'selected_id' => 'required|numeric',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'heure_start' => ['required'],
            'heure_end' => ['required'],
            'user_id' => 'required|array',
        ]);

        if ($this->selected_id) {
            $record = AstreinteModel::find($this->selected_id);

            $record->update([
                'date_start' => $this->date_start,
                'date_end' => $this->date_end,
                'heure_start' => $this->heure_start,
                'heure_end' => $this->heure_end,
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
            $record = AstreinteModel::where('id', $id)->first();
            $record->users()->detach();
            $record->delete();
            $this->updateMode = false;
            $this->getUsersHasAstreinte();
        }
    }

    public function getUsersHasAstreinte($search = false)
    {
        if ($search && $this->s_date_start !== '' && $this->s_date_end) {
            $this->searched = true;
            return AstreinteModel::has("users")
                                        ->where("date_start", ">=", $this->s_date_start)
                                        ->where("date_end", "<=", $this->s_date_end)
                                        ->paginate($this->perPage);
        }else {          
            $this->searched = false;
            return AstreinteModel::has("users")->paginate($this->perPage);
        }
    }

   
}

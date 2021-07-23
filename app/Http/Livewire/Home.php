<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\{Astreinte, Conge};
use DB;

class Home extends Component
{ 
    public $date_end, $date_start, $data, $statByMonth, $statByWeek, $s_week, $s_year, $conges;
    //Search 
    public $searchWeek, $searchYear = false;
    protected $listeners = [
        "getStatWeek"
    ];

    public function render()
    {

        $this->getUsersHasAstreinte();
        $this->getUsersHasConge();

        $statForMonth = ($this->searchYear) ? $this->getStatMonth(true) : $this->getStatMonth();
        $statForWeek = ($this->searchWeek) ? $this->getStatWeek(true) : $this->getStatWeek();

        return view('livewire.home',
                    [
                        "presentToday"=>$this->presentToday(),
                        "statForMonth" => $statForMonth,
                        "statForWeek" => $statForWeek,
                    ]);
    }
    
    public function getUsersHasAstreinte()
    {
        $date = new \DateTime();
        $this->date_start = $date->modify('this week')->format("Y-m-d");
        $date = new \DateTime();
        $this->date_end = $date->modify('this week +6 days')->format("Y-m-d");

        $this->data = Astreinte::has("users")
                                    ->where("date_start", ">=", $this->date_start)
                                    ->where("date_end", "<=", $this->date_end)
                                    ->get();
    }
    public function getUsersHasConge()
    {

        $this->conges = Conge::with(["users"=>function($q){
                                    $q->with('service');
                                }])
                                ->get();

    }

    public function presentToday()
    {
        $date = date('y-m-d');
        return DB::table('users AS u')
                ->join('presences AS p', 'p.user_id', 'u.id')
                ->whereRaw("DATE(p.created_at) = '$date' ")
                ->selectRaw("u.name as name, DATE_FORMAT(p.created_at, '%H:%i') as created_at, DATE_FORMAT(p.updated_at, '%H:%i') as updated_at")
                ->get();
    }

    public function getStatMonth($search = false)
    {
        $arr = [];
        $y = date("Y");        
        if($search && $this->s_year != ''){
            $y = intval($this->s_year);
         }
        $temp = DB::table('astreintes AS a')
                            ->join('astreinte_user AS au', 'au.astreinte_id', 'a.id')
                            ->join('users AS u', 'au.user_id', 'u.id')
                            //->select( DB::raw(' SEC_TO_TIME(SUM(TIME_TO_SEC(a.heure_end) - TIME_TO_SEC(a.heure_start))) AS hour'), 'u.name', DB::raw(' MONTH(a.created_at) month ')) 
                            //->select( DB::raw(' ROUND( SUM( TIME_TO_SEC(a.heure_end) - TIME_TO_SEC(a.heure_start)) / 3600, 2 ) AS hour' ), 'u.name', DB::raw(' MONTH(a.created_at) month ')) 
                            ->select( DB::raw(' SUM( nbr_hours) AS hour' ), 'u.name', DB::raw(' MONTH(a.date_end) month ')) 
                            ->groupby('u.name', 'month')
                            ->whereRaw(" YEAR(a.date_end) = $y" )
                            ->orderby('month')
                            ->get()
                            ->toArray();
        foreach ($temp as $item) {
            if ( empty($arr[ $item->name ]) ) {
                $arr[ $item->name ] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            }
            $arr[ $item->name ][  ($item->month-1) ] =   $item->hour ;
        }
        return $this->statByMonth = $arr;
    }

    public function getStatWeek($search = false)
    {
        $arr = [];
        //Get the day of the week using PHP's date function.
        $week = date("W");
        
        if($search && $this->s_week != ''){
            $week = intval($this->s_week);
        }

        $y = date("Y");

        $d = new \DateTime("NOW");
        $monday = $d->modify("monday this week")->format("Y-m-d");
        $d = new \DateTime("NOW");
        $friday = $d->modify("friday this week")->format("Y-m-d");
        $d = new \DateTime("NOW");
        $sunday = $d->modify("sunday this week")->format("Y-m-d");

        $temp = DB::table('astreintes AS a')
                            ->join('astreinte_user AS au', 'au.astreinte_id', 'a.id')
                            ->join('users AS u', 'au.user_id', 'u.id')
                            /*
                            ->select(
                                    DB::raw(' ROUND( SUM( TIME_TO_SEC(a.heure_end) - TIME_TO_SEC(a.heure_start)) / 3600, 2 ) AS hour' ), 'u.name', 
                                    DB::raw(' WEEKDAY(a.date_end) AS week ')
                            )
                            */
                            ->select(   
                                DB::raw(' SUM( nbr_hours ) AS totauxHour' ), 'u.name', 
                                DB::raw('  nbr_hours  AS hour' ),
                                DB::raw(' WEEKDAY(a.date_end) AS week ')
                            )
                            ->groupby('u.name', 'week', 'hour')    
                            ->whereRaw("DATE(a.date_start) >= '$monday' ")
                            ->whereRaw("DATE(a.date_end) <= '$sunday' ")
                            ->get()
                            ->toArray();
        //On créé un tableau de 7 jours avec 0h de présence
        $tempWeek = array_fill(0, 7, 0);
        //On remplit le tableau créé
        foreach ($temp as $item) {

            if ( empty($arr[ $item->name ]) ) {
                $arr[ $item->name." ($item->hour) " ] = array_fill(0, 7, 0);
            }

            foreach ($tempWeek as $key => $value) {
                if($key <= $item->week){
                    $arr[ $item->name." ($item->hour) " ][ $key ] = $item->totauxHour;
                }
            }
        }
        
        return $this->statByWeek = $arr;
    }

}

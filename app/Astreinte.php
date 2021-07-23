<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Astreinte extends Model
{
    public $timestamps = true;

    public $fillable = [
        "date_start",
        "date_end",
        "heure_start",
        "heure_end",
        "description",
        "nbr_hours",
    ];

    protected $casts = [
        "date_start" => "date",
        "date_end" => "date",
        "heure_start" => "time",
        "heure_end" => "time"
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}

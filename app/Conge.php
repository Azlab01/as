<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{
    protected $table = 'conges';
    public $timestamps = true;
    protected $fillable = [
        "date_start_conge", 
        "date_end_conge", 
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}

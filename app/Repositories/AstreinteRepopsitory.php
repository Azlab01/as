<?php

namespace App\Repositories;
use Illuminate\Http\Request;
use App\Astreinte;

class AstreinteRepository extends ResourceRepository
{

    public function __construct(Astreinte $astreinte)
    {
        $this->model = $astreinte;
    }

    
}
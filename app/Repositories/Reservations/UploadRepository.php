<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use App\Models\ReservationFollowUp;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Traits\DigitalOceanTrait;

class UploadRepository
{
    use DigitalOceanTrait;
    
    public function add($request)
    {        
        return $this->uploadMedia($request);
    }

    public function delete($request)
    {        
        return $this->deleteMedia($request);
    }
}
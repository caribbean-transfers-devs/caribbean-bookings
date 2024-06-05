<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * Relations
     */
    public function enterprise(){
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id');
    }    

    public function destination_service(){
        return $this->belongsTo(DestinationService::class, 'destination_service_id', 'id');
    }

    public function destination(){
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }
}

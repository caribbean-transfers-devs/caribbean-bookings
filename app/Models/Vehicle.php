<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * Relations
     */
    public function enterprise(){
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id')->withTrashed();
    }

    public function destination_service(){
        return $this->belongsTo(DestinationService::class, 'destination_service_id', 'id');
    }

    public function destination(){
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }
}

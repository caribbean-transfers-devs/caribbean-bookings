<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverSchedule extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'driver_schedules';

    public function vehicle(){
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    public function driver(){
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }    
}

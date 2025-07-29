<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverSchedule extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */    
    protected $fillable = [
        'id', 
        'date', 
        'check_in_time', 
        'check_out_time', 
        'end_check_out_time',
        'extra_hours',
        'vehicle_id',
        'driver_id',
        'status',
        'status_unit',
        'check_in_time_fleetio',
        'check_out_time_fleetio',
        'is_open',
        'observations'
    ];    

    protected $table = 'driver_schedules';

    public function vehicle(){
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    public function driver(){
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }    
}

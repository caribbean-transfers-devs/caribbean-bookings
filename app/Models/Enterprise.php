<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enterprise extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'destination_id',
    ];

    /**
     * Relations
    */
    public function destination(){
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }

    public function sites(){
        return $this->hasMany(Site::class, 'enterprise_id', 'id');
    }

    public function zones_enterprises(){
        return $this->hasMany(ZonesEnterprise::class, 'enterprise_id', 'id');
    }

    public function rates_enterprises(){
        return $this->hasMany(RatesEnterprise::class, 'enterprise_id', 'id');
    }

    public function vehicles(){
        return $this->hasMany(Vehicle::class, 'enterprise_id', 'id');
    }
    
    public function drivers(){
        return $this->hasMany(Driver::class, 'enterprise_id', 'id');
    }
}

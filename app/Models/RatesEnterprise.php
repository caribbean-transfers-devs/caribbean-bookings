<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatesEnterprise extends Model
{
    use HasFactory;

    protected $table = 'rates_enterprises';

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id');
    }

    public function destination(){
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }    

    public function destination_service(){
        return $this->belongsTo(DestinationService::class, 'destination_service_id', 'id');
    }

    public function zoneOne(){
        return $this->belongsTo(ZonesEnterprise::class, 'zone_one', 'id');
    }

    public function zoneTwo(){
        return $this->belongsTo(ZonesEnterprise::class, 'zone_two', 'id');
    }
}

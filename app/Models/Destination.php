<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Relations
     */
    
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'destination_id', 'id');
    }

    public function destination_services()
    {
        return $this->hasMany(DestinationService::class, 'destination_id', 'id');
    }

    public function from_destination()
    {
        return $this->hasMany(ReservationsItem::class, 'from_zone', 'id');
    }

    public function to_destination(){
        return $this->hasMany(ReservationsItem::class, 'to_zone', 'id');
    }
}

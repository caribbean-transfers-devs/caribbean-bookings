<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationsItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Relations
     */
    public function reservations()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'id');
    }

    public function destination_service()
    {
        return $this->belongsTo(DestinationService::class, 'destination_service_id', 'id');
    }

    public function origin(){
        return $this->belongsTo(Zones::class, 'from_zone', 'id');
    }

    public function destination(){
        return $this->belongsTo(Zones::class, 'to_zone', 'id');
    }
}

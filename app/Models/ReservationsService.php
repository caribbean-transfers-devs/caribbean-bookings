<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationsService extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Relations
     */

    public function items()
    {
        return $this->belongsTo(Reservation::class, 'reservation_item_id', 'id');
    }

    public function destinations()
    {
        return $this->belongsTo(Destination::class, 'destination_service_id', 'id');
    }
}

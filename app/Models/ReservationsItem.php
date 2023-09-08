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

    public function services()
    {
        return $this->hasMany(ReservationsService::class, 'reservation_item_id', 'id');
    }
}

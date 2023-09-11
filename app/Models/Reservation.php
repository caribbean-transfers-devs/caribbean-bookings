<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Relations
     */
    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(ReservationsItem::class, 'reservation_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'reservation_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'reservation_id', 'id');
    }

    public function followUps()
    {
        return $this->hasMany(ReservationFollowUp::class, 'reservation_id', 'id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }
}

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
        //return $this->hasMany(ReservationsItem::class, 'reservation_id', 'id');
        return $this->hasMany(ReservationsItem::class, 'reservation_id', 'id')
        ->join('zones', 'zones.id', '=', 'reservations_items.from_zone')
        ->select('reservations_items.*', 'reservations_items.id as reservations_item_id', 'zones.*');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'reservation_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'reservation_id', 'id');
    }

    // public function callCenterAgent()
    // {
    //     return $this->belongsTo(User::class, 'call_center_agent_id', 'id');
    // }

    public function callCenterAgent()
    {
        return $this->hasOneThrough(User::class, Sale::class, 'reservation_id', 'id', 'id', 'call_center_agent_id');
    }

    public function followUps()
    {
        return $this->hasMany(ReservationFollowUp::class, 'reservation_id', 'id')->orderBy('created_at', 'desc');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function cancellationType()
    {
        return $this->belongsTo(CancellationTypes::class, 'cancellation_type_id', 'id');
    }

    /*public function clip()
    {
        return $this->belongsTo(Clip::class);
    }*/
}

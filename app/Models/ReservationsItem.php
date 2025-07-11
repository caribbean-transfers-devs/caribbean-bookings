<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationsItem extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];    

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

    public function fromZone()
    {
        return $this->belongsTo(Zone::class, 'from_zone', 'id');
    }
    
    public function toZone()
    {
        return $this->belongsTo(Zone::class, 'to_zone', 'id');
    }    

    public function cancellationTypeOrigin(){
        return $this->belongsTo(CancellationTypes::class, 'op_one_cancellation_type_id', 'id');
    }

    public function cancellationTypeDestino(){
        return $this->belongsTo(CancellationTypes::class, 'op_two_cancellation_type_id', 'id');
    }
}

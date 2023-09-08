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
}

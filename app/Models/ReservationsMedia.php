<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationsMedia extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'reservations_media';
}

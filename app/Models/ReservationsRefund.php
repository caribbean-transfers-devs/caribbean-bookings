<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationsRefund extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'reservation_id',
        'message_refund',
        'status',
        'end_at',
        'link_refund',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }    
}

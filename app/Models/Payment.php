<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'id',
        'description',
        'total',
        'exchange_rate',
        'status',
        'operation',
        'payment_method',
        'currency',
        'object',
        'reservation_id',
        'reference',
        'is_conciliated',
        'is_conciliated_cash',
        'date_conciliation',
        'deposit_date',
        'total_fee',
        'total_net',
        'conciliation_comment'
    ];

    public function reservation(){
        return $this->belongsTo(Reservation::class);
    }
    
    public function clip(){
        return $this->belongsTo(Clip::class, 'clip_id', 'id');
    }
}

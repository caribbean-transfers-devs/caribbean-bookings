<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatorFeeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_fee_id',
        'user_id',
        'action',
        'old_data',
        'new_data',
        'notes'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array'
    ];
    
    public function operatorFee()
    {
        return $this->belongsTo(OperatorFee::class, 'operator_fee_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }    
}

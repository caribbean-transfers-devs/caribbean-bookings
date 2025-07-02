<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatorFee extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'base_amount',
        'commission_percentage',
        'zone_ids'
    ];

    protected $casts = [
        'zone_ids' => 'array'
    ];

    // Relación con los logs
    public function logs()
    {
        return $this->hasMany(OperatorFeeLog::class)->latest();
    }

    public function calculateCommission()
    {
        return $this->base_amount * ($this->commission_percentage / 100);
    }

    public static function getByZoneId($zoneId)
    {
        return static::whereJsonContains('zone_ids', (string)$zoneId)->first();
    }    
}

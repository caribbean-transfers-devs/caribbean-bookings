<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory,SoftDeletes;

    public $timestamps = false;

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function type()
    {
        return $this->belongsTo(SalesType::class, 'sale_type_id', 'id');
    }

    public function callCenterAgent()
    {
        return $this->belongsTo(User::class, 'call_center_agent_id', 'id');
    }
}

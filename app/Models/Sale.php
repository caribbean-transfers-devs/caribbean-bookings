<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function type()
    {
        return $this->belongsTo(SaleType::class, 'sale_type_id', 'id');
    }
}

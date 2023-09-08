<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesType extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function sales(){
        return $this->hasMany(Sale::class, 'sale_type_id', 'id');
    }
}

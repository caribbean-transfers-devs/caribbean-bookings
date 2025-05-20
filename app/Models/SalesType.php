<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesType extends Model
{
    use HasFactory,SoftDeletes;

    public function sales(){
        return $this->hasMany(Sale::class, 'sale_type_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enterprise extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * Relations
     */
    public function destination(){
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }    
}

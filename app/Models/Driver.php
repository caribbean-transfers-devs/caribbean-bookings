<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * Relations
     */    
    public function enterprise(){
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id')->withTrashed();
    }

    public function destination(){
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory,SoftDeletes;

    public $timestamps = false;

    public function reservation(){
        return $this->belongsTo(Reservation::class);
    }
    
    public function clip(){
        return $this->belongsTo(Clip::class, 'clip_id', 'id');
    }
}

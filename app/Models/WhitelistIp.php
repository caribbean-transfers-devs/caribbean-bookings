<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhitelistIp extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Relations
     */
    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}

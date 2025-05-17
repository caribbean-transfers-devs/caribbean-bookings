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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'destination_id',
    ];

    public function destination(){
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }

    public function sites(){
        return $this->hasMany(Site::class, 'enterprise_id', 'id');
    }
}

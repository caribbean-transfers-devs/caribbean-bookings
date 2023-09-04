<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submodule extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Relations
     */
    public function module(){
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }

    public function rolesPermits(){
        return $this->hasMany(RolesPermit::class, 'submodule_id', 'id');
    }
}

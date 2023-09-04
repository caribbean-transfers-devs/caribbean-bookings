<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Relations
     */
    public function submodules(){
        return $this->hasMany(Submodule::class, 'module_id', 'id');
    }
}

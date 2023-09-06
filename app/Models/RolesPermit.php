<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesPermit extends Model
{
    use HasFactory;

    public $timestamps = false;

    //table name
    protected $table = 'roles_permits';

    /**
     * Relations
     */
    public function role(){
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function permit(){
        return $this->belongsTo(Submodule::class, 'submodule_id', 'id');
    }
}

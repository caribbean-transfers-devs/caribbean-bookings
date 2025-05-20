<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['role']; // Agrega aquí los campos que deseas permitir

    /**
     * Relations
     */
    public function permits()
    {
        return $this->hasMany(RolesPermit::class, 'role_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(UserRole::class, 'role_id', 'id');
    }
}

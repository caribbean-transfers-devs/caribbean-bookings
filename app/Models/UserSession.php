<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  // Agregar este campo
        'ip_address',
        'user_agent',
        'device_name',        
        'last_activity',
        // Agrega otros campos necesarios
    ];    
}

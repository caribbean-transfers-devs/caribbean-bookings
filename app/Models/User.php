<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\RoleTrait;
use App\Traits\FiltersTrait;
use App\Traits\FinanceTrait;
use App\Traits\BookingTrait;
use App\Traits\OperationTrait;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, RoleTrait, FiltersTrait, FinanceTrait, BookingTrait, OperationTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relations
     */
    public function target(){
        return $this->belongsTo(Target::class, 'target_id', 'id');
    }

    public function sessions(){
        return $this->hasMany(UserSession::class, 'user_id', 'id');
    }

    public function roles(){
        return $this->hasMany(UserRole::class, 'user_id', 'id');
    }

    public function roles2()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }
    
    public function sales(){
        return $this->hasMany(Sale::class, 'call_center_agent_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject; // <- Add this

class User extends Authenticatable implements JWTSubject // <- implement JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phonenumber',
        'provider_id',
        'avatar'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ===== JWT Methods =====
    public function getJWTIdentifier()
    {
        return $this->getKey(); // usually user id
    }

    public function getJWTCustomClaims()
    {
        return []; // custom claims agar chahiye toh add kar sakte ho
    }
}

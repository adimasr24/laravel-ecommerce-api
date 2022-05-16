<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * products
     *
     * @return void
     */
    public function products() {
        return $this->hasMany(Product::class);
    }
    
    /**
     * getJWTIdentifier
     *
     * @return void
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }
    
    /**
     * getJWTCustomClaims
     *
     * @return void
     */
    public function getJWTCustomClaims() {
        return [];
    }
}

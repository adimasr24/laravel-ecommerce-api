<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'email_verified_at', 'password', 'remember_token'
    ];
    
    /**
     * invoices
     *
     * @return void
     */
    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    /**
     * reviews
     *
     * @return void
     */
    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function getCreatedAtAttribute($date) {
        $value = Carbon::parse($date);
        $parse = $value->locale('id');
        return $parse->translateFormat('1, d F Y');
    }

    
}

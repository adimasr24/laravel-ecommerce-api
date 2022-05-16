<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'invoice', 'customer_id', 'courier', 'courier_service', 'courier_cost', 'weight', 'name', 'phone', 'city_id', 'province_id', 'address', 'status', 'grand_total', 'snap_token' 
    ];
    
    /**
     * orders
     *
     * @return void
     */
    public function orders() {
        return $this->hasMany(Order::class);
    }
    
    /**
     * customer
     *
     * @return void
     */
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * city
     *
     * @return void
     */
    public function city() {
        // custom foreign key and primary key
        return $this->belongsTo(City::class, 'city_id', 'city_id');
    }

    /**
     * province
     *
     * @return void
     */
    public function province() {
        // custom foreign key and primary key
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

}

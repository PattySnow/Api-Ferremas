<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    
    protected $guard_name = 'api';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cart()
    {
        return $this->hasMany(Cart::class); // Utiliza hasOne en lugar de hasMany si es una relación de uno a uno
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shippingOrders()
    {
        return $this->hasMany(ShippingOrder::class);
    }
}

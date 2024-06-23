<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'cart_id',
        'subtotal',
        'shipping_cost',
        'total',
        'status',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}

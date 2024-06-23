<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Cart extends Model
{
    use HasFactory;
    protected $table = 'carts'; // Especificar el nombre de la tabla si es diferente al nombre convencional
    
    protected $fillable = [
        'user_id',
        'branch_id',
        'total',
        'status',
        'delivery_type'
    ];

    // Relación con productos a través de CarritoProducto
    public function Items()
    {
        return $this->belongsToMany(Item::class, 'cart_items')
                    ->withPivot('quantity', 'unit_price', 'subtotal');
    }


    // Relación con la compra asociada
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function shippingOrders()
    {
        return $this->hasMany(ShippingOrder::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}

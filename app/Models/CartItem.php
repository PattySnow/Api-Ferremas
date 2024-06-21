<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'item_id', 'branch_id', 'quantity', 'unit_price', 'subtotal', 'total'];
    protected $table = 'cart_items';

    public function product()
    {
        return $this->belongsTo(Item::class);
    }
}

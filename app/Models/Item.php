<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id'
    ] ;

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items')
                    ->withPivot('quantity', 'unit_price', 'subtotal', 'branch_id');
    }


    public function categories()
    {
        return $this->belongsTo(Category::class);
    }
}

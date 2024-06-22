<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $table = 'branches';

    protected $fillable = ['name', 'address'];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'inventories')
                    ->withPivot('quantity');
    }

    
}

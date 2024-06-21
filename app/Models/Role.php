<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Role extends Model
{
    use HasRoles;

    protected $fillable = [
        'name',
        'guard_name',
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }
}

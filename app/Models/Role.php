<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = "ms_roles";
    protected $guarded = ['id'];

    public function menus()
    {
        return $this->hasMany(RoleMenu::class);
    }

    public function permissions()
    {
        return $this->hasMany(RolePermission::class);
    }
}

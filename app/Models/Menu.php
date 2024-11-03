<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = "ms_menus";
    protected $guarded = ['id'];

    public function childrens()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}

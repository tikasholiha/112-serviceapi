<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    use HasFactory;

    protected $table = "tr_role_menus";
    protected $guarded = ['id'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = "ms_permissions";
    protected $guarded = ['id'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}

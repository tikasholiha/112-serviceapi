<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = "tr_user_roles";
    protected $guarded = ['id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}

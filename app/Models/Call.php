<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $table = "ms_calls";
    protected $guarded = ['id'];

    public function detail()
    {
        return $this->hasMany(CallDetail::class);
    }
}

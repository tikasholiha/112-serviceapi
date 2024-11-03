<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emergency extends Model
{
    use HasFactory;

    protected $table = "ms_emergency";
    protected $guarded = ['id'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = "ms_employees";
    protected $guarded = ['id'];

    public function marital_status()
    {
        return $this->belongsTo(Status::class);
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }
}

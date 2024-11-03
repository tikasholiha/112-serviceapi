<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCall extends Model
{
    use HasFactory;

    protected $table = "ms_test_calls";
    protected $guarded = ['id'];
}

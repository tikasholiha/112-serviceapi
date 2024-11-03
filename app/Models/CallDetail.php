<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallDetail extends Model
{
    use HasFactory;

    protected $table = "tr_call_details";
    protected $guarded = ['id'];
}

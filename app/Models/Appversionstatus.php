<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appversionstatus extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'appversionstatus';
}

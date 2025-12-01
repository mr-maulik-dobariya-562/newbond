<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingHours extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name',"branch_id"];
}

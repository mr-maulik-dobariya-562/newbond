<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cartoon extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'size',
        'created_by',
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}

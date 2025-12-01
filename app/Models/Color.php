<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'color',
        'font_color',
        'created_by',
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

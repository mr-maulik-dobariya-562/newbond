<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'message',
        'image',
        'created_by',
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(Customer::class, 'created_by');
    }
}

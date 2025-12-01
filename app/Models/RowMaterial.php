<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RowMaterial extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', "branch_id", 'created_by'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintType extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected  $table = 'print_type';

    protected $fillable = [
        'name',
        'short_name',
        "branch_id",
        'created_by'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

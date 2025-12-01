<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemGroupPrintExtra extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable  = [
        "print_extra_id",
        "amount",
        "item_group_id",
        "created_by",
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function printExtra()
    {
        return $this->belongsTo(PrintTypeExtra::class, "print_extra_id", "id");
    }
}

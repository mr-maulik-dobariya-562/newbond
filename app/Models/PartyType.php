<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartyType extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable  = [
        "name",
        "color",
        "item_discount",
        "item_price",
        "extra_price",
        "created_by",
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}

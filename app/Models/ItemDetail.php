<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDetail extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id',
        'print_type_id',
        'checkbox',
        'created_by',
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function item()
    {
        return $this->belongsTo(Item::class, "item_id");
    }

    public function printType()
    {
        return $this->belongsTo(PrintType::class, "print_type_id");
    }
}

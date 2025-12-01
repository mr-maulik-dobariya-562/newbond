<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemGroupPrice extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected  $table = 'item_group_price';

    protected $fillable  = [
        "print_type_id",
        "extra_price",
        "usd_extra_price",
        "item_group_id",
        "branch_id",
    ];

    public function cretedBy()
    {
        return $this->belongsTo(User::class, "created_by", "id");
    }

    public function printType()
    {
        return $this->belongsTo(PrintType::class, "print_type_id", "id");
    }
}

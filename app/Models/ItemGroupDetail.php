<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemGroupDetail extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected  $table = 'item_group_detail';

    protected $fillable  = [
        "item_group_id",
        "min_dealer",
        "total_dealer",
        "min_retail",
        "total_retail",
        "print_type_id",
        "created_by",
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

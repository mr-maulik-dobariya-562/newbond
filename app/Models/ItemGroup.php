<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemGroup extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected  $table = 'item_group';

    protected $fillable  = [
        "group_name",
        "bill_title",
        "sequence_number",
        "gst",
        "retail_wp_available",
        "case_type_id",
        "created_by",
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function details()
    {
        return $this->hasMany(ItemGroupDetail::class, "item_group_id", "id");
    }

    public function prices()
    {
        return $this->hasMany(ItemGroupPrice::class, "item_group_id", "id");
    }

    public function itemCategorie()
    {
        return $this->hasMany(ItemCategory::class, "item_group_id", "id");
    }
}

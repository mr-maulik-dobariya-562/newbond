<?php

namespace App\Models;

use App\Helpers\FileUpload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemCategory extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'retail_sequence',
        'description',
        'item_group_id',
        'size_id',
        'status',
        'image',
        'created_by',
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function itemGroup()
    {
        return $this->belongsTo(ItemGroup::class, "item_group_id");
    }

    public function item()
    {
        return $this->hasMany(Item::class, "categories_id");
    }

    public function size()
    {
        return $this->belongsTo(Size::class, "size_id");
    }

    public function status()
    {
        return $this->status == "ACTIVE" ? "<div class='badge bg-blue-lt'>ACTIVE</div>" : "<div class='badge bg-danger-lt'>INACTIVE</div>";
    }

    public function viewUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return FileUpload::url($this->image, "itemCategory");
            }
        );
    }
}

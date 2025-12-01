<?php

namespace App\Models;

use App\Helpers\FileUpload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $folder = "item";
    protected $fillable = [
        'name',
        'categories_id',
        'extra_retail_discount',
        'extra_dealer_discount',
        'dealer_current_price',
        'retail_current_price',
        'usd_current_price',
        'dealer_old_price',
        'retail_old_price',
        'usd_old_price',
        'local_size_id',
        'export_size_id',
        'export_weight',
        'local_weight',
        'minimum_qty',
        'packing',
        'type',
        'active_type',
        "cartoon_type",
        'image',
        'created_by',
        "branch_id",
    ];

    public function itemDetails()
    {
        return $this->hasMany(ItemDetail::class, 'item_id');
    }

    public function categories()
    {
        return $this->belongsTo(ItemCategory::class, 'categories_id');
    }

    public function printTypes()
    {
        return $this->belongsToMany(PrintType::class, 'item_details', 'item_id', 'print_type_id');
    }

    public function estimateDetail()
    {
        return $this->hasMany(EstimateDetail::class, 'item_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function viewUrl(): Attribute
    {

        return Attribute::make(

            get: function ($value) {

                return FileUpload::url($this->image, "$this->folder");
            }

        );
    }

    public function ListingImage()
    {
        return FileUpload::url($this->image, "$this->folder", "_200");
    }
}

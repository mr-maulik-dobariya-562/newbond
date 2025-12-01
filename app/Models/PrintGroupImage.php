<?php

namespace App\Models;

use App\Helpers\FileUpload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintGroupImage extends Model
{
    use HasFactory;

    protected $folder = "printGroupImage";
    protected $fillable = [
        'print_type_id',
        'item_group_id',
        'image',
        'created_by',
        'updated_by',
        "branch_id",
    ];

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

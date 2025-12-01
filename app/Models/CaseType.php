<?php

namespace App\Models;

use App\Helpers\FileUpload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseType extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $folder = "case_type";
    protected $fillable = [
        "title",
        "image",
        "sequence_number",
        "is_active",
        "branch_id",
        "created_by",
        "updated_by",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
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

<?php

namespace App\Models;

use App\Helpers\FileUpload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Arrival extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $folder = "arrival";
    protected $fillable = [
        "name",
        "sequence",
        "created_by",
        "status",
        "country_id",
        "branch_id",
        "image",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function country()
    {
        return $this->belongsTo(Country::class, "country_id");
    }

    public function status()
    {
        return $this->status == "ACTIVE" ? "<div class='badge bg-blue-lt'>ACTIVE</div>" : "<div class='badge bg-danger-lt'>INACTIVE</div>";
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

<?php

namespace App\Models;

use App\Helpers\FileUpload;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceList extends BaseModel
{
	use HasFactory, SoftDeletes;

	protected $folder = "pricelist";

	protected $table = "price_lists";

	protected $fillable = [
		'id',
		'title',
		'party_type_id',
		'image',
        "branch_id",
		'created_by'
	];

	public function partyType()
	{
		return $this->belongsTo(PartyType::class, "party_type_id");
	}

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

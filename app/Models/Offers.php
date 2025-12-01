<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offers extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "text",
        "party_type_id",
        "value",
        "value_type",
        "created_by",
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function partyType()
    {
        return $this->belongsTo(PartyType::class, "party_type_id");
    }
}

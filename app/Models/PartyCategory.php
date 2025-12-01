<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartyCategory extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = "party_categorys";

    protected $fillable = [
        "name",
        "color",
        "branch_id",
        "created_by",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}

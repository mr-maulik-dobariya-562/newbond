<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartyGroup extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = "party_groups";
    protected $fillable = [
        "name",
        "created_by",
        "branch_id",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}

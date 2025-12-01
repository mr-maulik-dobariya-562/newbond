<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "party_group_id",
        "title",
        "message",
        "is_read",
        "branch_id",
        "created_by",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function partyGroup()
    {
        return $this->belongsTo(PartyGroup::class, "party_group_id");
    }
}

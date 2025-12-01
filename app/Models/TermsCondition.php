<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        "text",
        "party_type_id",
        "branch_id",
        "created_by",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function partyType()
    {
        return $this->belongsTo(PartyType::class, 'party_type_id');
    }
}

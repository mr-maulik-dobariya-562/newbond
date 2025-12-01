<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'customer_id',
        'party_type_id',
        'date',
        'remark',
        "created_by",
        "created_at",
        "updated_at",
        'status',
        "branch_id",
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function partyType()
    {
        return $this->belongsTo(PartyType::class, "party_type_id");
    }
}

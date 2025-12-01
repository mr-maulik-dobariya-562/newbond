<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "bank_id",
        "amount",
        "payment_type",
        "type",
        "remark",
        "date",
        "created_by",
        "customer_id",
        "bank_id",
        "branch_id",
        "number"
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, "bank_id");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends BaseModel
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'amount',
        'ref_id',
        'date',
        'txn_type_id',
        'type',
        "branch_id",
        'remark',
        'created_at',
        'deleted_at',
    ];

    public function createdBy()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, "user_id");
    }
    public function txnType()
    {
        return $this->belongsTo(TxnType::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transport extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name",
        "branch_id",
        "remark",
        'is_waybill',
        "created_by",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function branches()
    {
        return $this->hasMany(TransportDetail::class, "transport_id");
    }

    public function wayBillLabel() {
        return $this->is_waybill ? '<span class="badge bg-success-lt">Yes</span>' : '<span class="badge bg-red-lt">No</span>';
    }
}

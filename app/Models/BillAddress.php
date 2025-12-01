<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillAddress extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "firm_name",
        "gst_no",
        "pan_no",
        "address",
        "branch_id",
        "customer_id",
    ];
}

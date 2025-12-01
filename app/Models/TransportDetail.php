<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportDetail extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $fillable = [
        "transport_id",
        "branch",
        "branch_id",
        "contact_no",
    ];

    public function transport()
    {
        return $this->belongsTo(Transport::class, "transport_id");
    }
}

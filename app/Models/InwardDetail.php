<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InwardDetail extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'inward_details';

    protected $fillable = [
        'inward_id',
        'item_id',
        'qty',
        'remark',
        'is_special',
        'parcel',
        "branch_id",
        'created_by'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationDetail extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id',
        'item_name',
        'print_type_id',
        'qty',
        'rate',
        'block',
        'narration',
        'remark',
        'transport_id',
        'amount',
        'date',
        'design',
        'discount',
        'status',
        'dispatch_qty',
        'quotation_id',
        'created_by',
        "branch_id",
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function printType()
    {
        return $this->belongsTo(PrintType::class, 'print_type_id');
    }
}

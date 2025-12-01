<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id',
        'item_name',
        'print_type_id',
        'print_type_other_id',
        'qty',
        'cancel_qty',
        'rate',
        'block',
        'narration',
        'remark',
        'other_remark',
        'transport_id',
        'amount',
        'date',
        'design',
        'discount',
        'status',
        'is_special',
        'dispatch_qty',
        'order_id',
        'created_by',
        'updated_by',
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

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function printType()
    {
        return $this->belongsTo(PrintType::class, 'print_type_id');
    }

    public function printTypeOther()
    {
        return $this->belongsTo(PrintTypeExtra::class, 'print_type_other_id');
    }
}

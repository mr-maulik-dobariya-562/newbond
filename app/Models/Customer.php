<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = "customer";
    protected $fillable = [
        'name',
        'parent_id',
        'mobile',
        'email',
        'balance',
        "country_id",
        "state_id",
        "city_id",
        "address",
        "contact_person",
        "area",
        "pincode",
        "password",
        "pay_terms",
        "party_type_id",
        "party_group_id",
        "bill_group_id",
        "gst",
        "pan_no",
        "price",
        "discount",
        "bill_type",
        "other_sample",
        "other_courier_id",
        "other_transport_id",
        "other_reason_remark",
        "status",
        "fax",
        "vat",
        "reference",
        'created_by',
        "branch_id",
    ];


    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function other_state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function partyCategory()
    {
        return $this->belongsTo(PartyCategory::class, 'bill_type');
    }

    public function other_city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
    public function partyType()
    {
        return $this->belongsTo(PartyType::class, "party_type_id");
    }

    public function parent()
    {
        return $this->belongsTo(Customer::class, "parent_id");
    }
    public function status()
    {
        return $this->status == "ACTIVE" ? "<div class='badge bg-blue-lt'>ACTIVE</div>" : "<div class='badge bg-danger-lt'>INACTIVE</div>";
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class, "other_courier_id");
    }
    public function transport()
    {
        return $this->belongsTo(Transport::class, "other_transport_id");
    }

    public function billAddress()
    {
        return $this->belongsTo(BillAddress::class, "customer_id");
    }

    public function partyGroup()
    {
        return $this->belongsTo(PartyGroup::class, "party_group_id");
    }

    public function displayName()
    {
        return $this->name;
    }
}

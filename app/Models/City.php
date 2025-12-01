<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'state_id',
        'country_id',
        "created_by",
        "branch_id",
    ];

    public function state()
    {
        return $this->belongsTo(State::class, "state_id");
    }

    public function country()
    {
        return $this->belongsTo(Country::class, "country_id");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}

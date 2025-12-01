<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["name", "machine_type_id", "location_id", "branch_id", "created_by"];

    public function machineType()
    {
        return $this->belongsTo(MachineType::class, "machine_type_id");
    }

    public function location()
    {
        return $this->belongsTo(Location::class, "location_id");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}

<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	protected static function booted()
	{
        // Automatically set branch_id
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->branch_id = session('branch_id');
            }
        });

        // Automatically set branch_id
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->branch_id = session('branch_id');
            }
        });

        // Automatically set branch_id
        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->branch_id = session('branch_id');
            }
        });

        // Automatically set branch_id
        static::restoring(function ($model) {
            if (auth()->check()) {
                $model->branch_id = session('branch_id');
            }
        });

        // Automatically set branch_id
        static::forceDeleted(function ($model) {
            if (auth()->check()) {
                $model->branch_id = session('branch_id');
            }
        });

		static::addGlobalScope('branch', function (\Illuminate\Database\Eloquent\Builder $builder) {
            if (auth()->check()) {
                $branchId = session('branch_id');

                $table = $builder->getModel()->getTable();
                $builder->where("{$table}.branch_id", $branchId);
            }
        });

        // static::addGlobalScope(new BranchScope());
	}
}

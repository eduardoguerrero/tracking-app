<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class BranchModel extends Model
{
    protected $table = 'branches';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function couriers()
    {
        return $this->hasMany(CourierModel::class, 'branch_id');
    }

    public function vehicles()
    {
        return $this->hasMany(VehicleModel::class, 'branch_id');
    }

    public function packages()
    {
        return $this->hasMany(PackageModel::class, 'branch_id');
    }
}

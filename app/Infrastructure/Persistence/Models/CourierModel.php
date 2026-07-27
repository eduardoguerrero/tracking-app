<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class CourierModel extends Model
{
    protected $table = 'couriers';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'branch_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id');
    }

    public function vehicles()
    {
        return $this->hasMany(VehicleModel::class, 'courier_id');
    }

    public function packages()
    {
        return $this->hasMany(PackageModel::class, 'courier_id');
    }
}

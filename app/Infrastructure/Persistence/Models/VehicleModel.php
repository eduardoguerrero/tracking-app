<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    protected $table = 'vehicles';

    protected $fillable = [
        'plate_number',
        'type',
        'brand',
        'model',
        'courier_id',
        'branch_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function courier()
    {
        return $this->belongsTo(CourierModel::class, 'courier_id');
    }

    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id');
    }

    public function packages()
    {
        return $this->hasMany(PackageModel::class, 'vehicle_id');
    }
}

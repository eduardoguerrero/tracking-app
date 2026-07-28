<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class PackageModel extends Model
{
    protected $table = 'packages';

    protected $fillable = [
        'tracking_number',
        'description',
        'weight',
        'status',
        'delivery_address',
        'recipient_name',
        'recipient_phone',
        'branch_id',
        'courier_id',
        'vehicle_id',
        'delivered_at',
    ];

    protected $casts = [
        'weight' => 'float',
        'delivered_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(BranchModel::class, 'branch_id');
    }

    public function courier()
    {
        return $this->belongsTo(CourierModel::class, 'courier_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(StatusHistoryModel::class, 'package_id');
    }

    public function scopeByTrackingNumber($query, string $trackingNumber)
    {
        return $query->where('tracking_number', $trackingNumber);
    }
}

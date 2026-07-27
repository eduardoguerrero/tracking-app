<?php

namespace App\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class StatusHistoryModel extends Model
{
    protected $table = 'status_histories';

    public $timestamps = false;

    protected $fillable = [
        'package_id',
        'previous_status',
        'new_status',
        'comment',
        'location',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function package()
    {
        return $this->belongsTo(PackageModel::class, 'package_id');
    }
}

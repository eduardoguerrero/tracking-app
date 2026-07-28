<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Package;
use App\Domain\Entities\TrackingHistory;

interface PackageRepositoryInterface
{
    public function save(Package $package): Package;

    public function findByTrackingNumber(string $trackingNumber): ?Package;

    public function findByTrackingNumberOrFail(string $trackingNumber): Package;

    public function updateStatus(Package $package): Package;

    /** @return TrackingHistory[] */
    public function getStatusHistory(int $packageId): array;

    public function addStatusHistory(TrackingHistory $history): TrackingHistory;
}

<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Entities\Package;
use App\Domain\Entities\TrackingHistory;
use App\Domain\Enums\PackageStatusEnum;
use App\Domain\Exceptions\PackageNotFoundException;
use App\Domain\Repositories\PackageRepositoryInterface;
use App\Infrastructure\Persistence\Models\PackageModel as PackageEloquent;
use App\Infrastructure\Persistence\Models\StatusHistoryModel as StatusHistoryEloquent;

class EloquentPackageRepository implements PackageRepositoryInterface
{
    public function save(Package $package): Package
    {
        $model = new PackageEloquent();
        $model->tracking_number = $package->trackingNumber;
        $model->description = $package->description;
        $model->weight = $package->weight;
        $model->status = $package->status->value;
        $model->branch_id = $package->branchId;
        $model->courier_id = $package->courierId;
        $model->vehicle_id = $package->vehicleId;
        $model->delivery_address = $package->deliveryAddress;
        $model->recipient_name = $package->recipientName;
        $model->recipient_phone = $package->recipientPhone;
        $model->delivered_at = $package->deliveredAt;
        $model->save();

        return $this->toDomainEntity($model);
    }

    public function findByTrackingNumber(string $trackingNumber): ?Package
    {
        $model = PackageEloquent::byTrackingNumber($trackingNumber)->first();

        if (!$model) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    public function findByTrackingNumberOrFail(string $trackingNumber): Package
    {
        $package = $this->findByTrackingNumber($trackingNumber);

        if (!$package) {
            throw new PackageNotFoundException($trackingNumber);
        }

        return $package;
    }

    public function updateStatus(Package $package): Package
    {
        $model = PackageEloquent::byTrackingNumber($package->trackingNumber)->firstOrFail();

        $model->status = $package->status->value;
        $model->courier_id = $package->courierId;
        $model->vehicle_id = $package->vehicleId;
        $model->delivered_at = $package->deliveredAt;
        $model->save();

        return $this->toDomainEntity($model);
    }

    public function getStatusHistory(int $packageId): array
    {
        return StatusHistoryEloquent::where('package_id', $packageId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($model) => $this->toHistoryEntity($model))
            ->all();
    }

    public function addStatusHistory(TrackingHistory $history): TrackingHistory
    {
        $model = StatusHistoryEloquent::create([
            'package_id' => $history->packageId,
            'previous_status' => $history->previousStatus,
            'new_status' => $history->newStatus,
            'comment' => $history->comment,
            'location' => $history->location,
        ]);

        return $this->toHistoryEntity($model);
    }

    private function toDomainEntity(PackageEloquent $model): Package
    {
        return new Package(
            id: $model->id,
            trackingNumber: $model->tracking_number,
            description: $model->description,
            weight: $model->weight,
            status: PackageStatusEnum::fromString($model->status),
            branchId: $model->branch_id,
            courierId: $model->courier_id,
            vehicleId: $model->vehicle_id,
            deliveryAddress: $model->delivery_address,
            recipientName: $model->recipient_name,
            recipientPhone: $model->recipient_phone,
            deliveredAt: $model->delivered_at?->toDateTimeString(),
            createdAt: $model->created_at?->toDateTimeString(),
            updatedAt: $model->updated_at?->toDateTimeString(),
        );
    }

    private function toHistoryEntity(StatusHistoryEloquent $model): TrackingHistory
    {
        return new TrackingHistory(
            id: $model->id,
            packageId: $model->package_id,
            previousStatus: $model->previous_status,
            newStatus: $model->new_status,
            comment: $model->comment,
            location: $model->location,
            createdAt: $model->created_at?->toDateTimeString(),
        );
    }
}

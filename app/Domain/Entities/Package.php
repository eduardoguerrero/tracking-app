<?php

namespace App\Domain\Entities;

use App\Domain\Enums\PackageStatusEnum;

class Package
{
    public function __construct(
        public readonly string $trackingNumber,
        public string $description,
        public ?float $weight,
        public PackageStatusEnum $status,
        public int $branchId,
        public ?int $courierId = null,
        public ?int $vehicleId = null,
        public ?string $deliveryAddress = null,
        public ?string $recipientName = null,
        public ?string $recipientPhone = null,
        public ?string $deliveredAt = null,
        public readonly ?int $id = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {
    }

    public function updateStatus(PackageStatusEnum $newStatus, ?int $courierId = null, ?int $vehicleId = null): void
    {
        if (!$this->status->canTransitionTo($newStatus)) {
            throw new \App\Domain\Exceptions\InvalidStatusTransitionException(
                $this->status->value,
                $newStatus->value
            );
        }

        if ($newStatus->requiresAssignedCourierAndVehicle()) {
            if ($courierId === null || $vehicleId === null) {
                throw new \App\Domain\Exceptions\InvalidStatusTransitionException(
                    $this->status->value,
                    $newStatus->value,
                    'An active courier and vehicle are required to change to "In Transit"'
                );
            }
        }

        $this->status = $newStatus;

        if ($courierId !== null) {
            $this->courierId = $courierId;
        }

        if ($vehicleId !== null) {
            $this->vehicleId = $vehicleId;
        }
    }

    public function markAsDelivered(): void
    {
        $this->updateStatus(PackageStatusEnum::Delivered);
        $this->deliveredAt = now()->toDateTimeString();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tracking_number' => $this->trackingNumber,
            'description' => $this->description,
            'weight' => $this->weight,
            'status' => $this->status->value,
            'branch_id' => $this->branchId,
            'courier_id' => $this->courierId,
            'vehicle_id' => $this->vehicleId,
            'delivery_address' => $this->deliveryAddress,
            'recipient_name' => $this->recipientName,
            'recipient_phone' => $this->recipientPhone,
            'delivered_at' => $this->deliveredAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}

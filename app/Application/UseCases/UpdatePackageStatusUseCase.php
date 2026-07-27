<?php

namespace App\Application\UseCases;

use App\Application\DTOs\UpdatePackageStatusDTO;
use App\Application\Responses\PackageResponse;
use App\Domain\Entities\TrackingHistory;
use App\Domain\Repositories\PackageRepositoryInterface;

class UpdatePackageStatusUseCase
{
    public function __construct(
        private readonly PackageRepositoryInterface $packageRepository,
    ) {
    }

    public function execute(string $trackingNumber, UpdatePackageStatusDTO $dto): PackageResponse
    {
        $package = $this->packageRepository->findByTrackingNumberOrFail($trackingNumber);

        $previousStatus = $package->status->value;

        $package->updateStatus(
            newStatus: $dto->newStatus,
            courierId: $dto->courierId,
            vehicleId: $dto->vehicleId,
        );

        $updatedPackage = $this->packageRepository->updateStatus($package);

        $this->packageRepository->addStatusHistory(new TrackingHistory(
            packageId: $updatedPackage->id,
            previousStatus: $previousStatus,
            newStatus: $updatedPackage->status->value,
            comment: $dto->comment,
            location: $dto->location,
        ));

        $history = $this->packageRepository->getStatusHistory($updatedPackage->id);

        return PackageResponse::fromPackage($updatedPackage, $history);
    }
}

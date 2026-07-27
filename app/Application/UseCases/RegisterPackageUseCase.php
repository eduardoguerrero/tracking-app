<?php

namespace App\Application\UseCases;

use App\Application\DTOs\RegisterPackageDTO;
use App\Application\Responses\PackageResponse;
use App\Domain\Entities\Package;
use App\Domain\Enums\PackageStatusEnum;
use App\Domain\Repositories\PackageRepositoryInterface;
use App\Domain\Entities\TrackingHistory;

class RegisterPackageUseCase
{
    public function __construct(
        private readonly PackageRepositoryInterface $packageRepository,
    ) {
    }

    public function execute(RegisterPackageDTO $dto): PackageResponse
    {
        $package = new Package(
            trackingNumber: $dto->trackingNumber,
            description: $dto->description,
            weight: $dto->weight,
            status: PackageStatusEnum::Registered,
            branchId: $dto->branchId,
            deliveryAddress: $dto->deliveryAddress,
            recipientName: $dto->recipientName,
            recipientPhone: $dto->recipientPhone,
        );

        $savedPackage = $this->packageRepository->save($package);

        $this->packageRepository->addStatusHistory(new TrackingHistory(
            packageId: $savedPackage->id,
            previousStatus: null,
            newStatus: PackageStatusEnum::Registered->value,
            comment: 'Package registered in the system',
        ));

        $history = $this->packageRepository->getStatusHistory($savedPackage->id);

        return PackageResponse::fromPackage($savedPackage, $history);
    }
}

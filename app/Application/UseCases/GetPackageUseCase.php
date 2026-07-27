<?php

namespace App\Application\UseCases;

use App\Application\Responses\PackageResponse;
use App\Domain\Repositories\PackageRepositoryInterface;

class GetPackageUseCase
{
    public function __construct(
        private readonly PackageRepositoryInterface $packageRepository,
    ) {
    }

    public function execute(string $trackingNumber): PackageResponse
    {
        $package = $this->packageRepository->findByTrackingNumberOrFail($trackingNumber);
        $history = $this->packageRepository->getStatusHistory($package->id);

        return PackageResponse::fromPackage($package, $history);
    }
}

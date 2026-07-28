<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\DTOs\RegisterPackageDTO;
use App\Application\DTOs\UpdatePackageStatusDTO;
use App\Application\UseCases\GetPackageUseCase;
use App\Application\UseCases\RegisterPackageUseCase;
use App\Application\UseCases\UpdatePackageStatusUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPackageRequest;
use App\Http\Requests\UpdatePackageStatusRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Response;

final class PackageController extends Controller
{
    public function __construct(
        private readonly RegisterPackageUseCase $registerPackage,
        private readonly GetPackageUseCase $getPackage,
        private readonly UpdatePackageStatusUseCase $updatePackageStatus,
    ) {
    }

    public function store(RegisterPackageRequest $request)
    {
        $dto = RegisterPackageDTO::fromArray($request->validated());

        $response = $this->registerPackage->execute($dto);

        return ApiResponse::success($response->toArray()['data'], 'Package registered successfully', Response::HTTP_CREATED);
    }

    public function show(string $trackingNumber)
    {
        $response = $this->getPackage->execute($trackingNumber);

        return ApiResponse::success($response->toArray()['data'], 'Package found');
    }

    public function updateStatus(string $trackingNumber, UpdatePackageStatusRequest $request)
    {
        $dto = UpdatePackageStatusDTO::fromArray($request->validated());

        $response = $this->updatePackageStatus->execute($trackingNumber, $dto);

        return ApiResponse::success($response->toArray()['data'], 'Status updated successfully');
    }
}

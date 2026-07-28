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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

final class PackageController extends Controller
{
    public function __construct(
        private readonly RegisterPackageUseCase $registerPackage,
        private readonly GetPackageUseCase $getPackage,
        private readonly UpdatePackageStatusUseCase $updatePackageStatus,
    ) {
    }

    public function store(RegisterPackageRequest $request): JsonResponse
    {
        $dto = RegisterPackageDTO::fromArray($request->validated());

        $response = $this->registerPackage->execute($dto);

        Log::info('Package registered', ['tracking_number' => $dto->trackingNumber]);

        return ApiResponse::success(
            $response->toArray()['data'],
            'Package registered successfully',
            Response::HTTP_CREATED
        );
    }

    public function show(string $trackingNumber): JsonResponse
    {
        $response = $this->getPackage->execute($trackingNumber);

        Log::info('Package queried', ['tracking_number' => $trackingNumber]);

        return ApiResponse::success($response->toArray()['data'], 'Package found');
    }

    public function updateStatus(string $trackingNumber, UpdatePackageStatusRequest $request): JsonResponse
    {
        $dto = UpdatePackageStatusDTO::fromArray($request->validated());

        $response = $this->updatePackageStatus->execute($trackingNumber, $dto);

        Log::info('Package status updated', [
            'tracking_number' => $trackingNumber,
            'new_status' => $dto->newStatus->value,
        ]);

        return ApiResponse::success($response->toArray()['data'], 'Status updated successfully');
    }
}

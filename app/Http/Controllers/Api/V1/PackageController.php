<?php

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
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Packages',
    description: 'Package registration, tracking, and status management'
)]
class PackageController extends Controller
{
    public function __construct(
        private readonly RegisterPackageUseCase $registerPackage,
        private readonly GetPackageUseCase $getPackage,
        private readonly UpdatePackageStatusUseCase $updatePackageStatus,
    ) {
    }

    #[OA\Post(
        path: '/api/v1/packages',
        operationId: 'registerPackage',
        summary: 'Register a new package',
        description: 'Creates a new package in the system with initial "Registered" status. Requires a unique tracking number.',
        security: [['bearerAuth' => []]],
        tags: ['Packages'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['tracking_number', 'description', 'branch_id'],
                properties: [
                    new OA\Property(
                        property: 'tracking_number',
                        type: 'string',
                        maxLength: 50,
                        example: 'AF-PKG-202501',
                        description: 'Unique tracking number for the package'
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        maxLength: 255,
                        example: 'Dell XPS Laptop for urgent delivery',
                        description: 'Package contents description'
                    ),
                    new OA\Property(
                        property: 'weight',
                        type: 'number',
                        format: 'float',
                        nullable: true,
                        example: 2.50,
                        description: 'Package weight in kg'
                    ),
                    new OA\Property(
                        property: 'branch_id',
                        type: 'integer',
                        example: 1,
                        description: 'ID of the branch where the package is registered'
                    ),
                    new OA\Property(
                        property: 'delivery_address',
                        type: 'string',
                        maxLength: 255,
                        nullable: true,
                        example: 'Calle Madero 123, Col. Centro',
                        description: 'Destination delivery address'
                    ),
                    new OA\Property(
                        property: 'recipient_name',
                        type: 'string',
                        maxLength: 150,
                        nullable: true,
                        example: 'Roberto Sánchez',
                        description: 'Name of the recipient'
                    ),
                    new OA\Property(
                        property: 'recipient_phone',
                        type: 'string',
                        maxLength: 20,
                        nullable: true,
                        example: '55-4000-0001',
                        description: 'Phone number of the recipient'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Package registered successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Package registered successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'package',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer'),
                                        new OA\Property(property: 'tracking_number', type: 'string'),
                                        new OA\Property(property: 'description', type: 'string'),
                                        new OA\Property(property: 'weight', type: 'number', format: 'float', nullable: true),
                                        new OA\Property(property: 'status', type: 'string', example: 'Registered'),
                                        new OA\Property(property: 'branch_id', type: 'integer'),
                                        new OA\Property(property: 'courier_id', type: 'integer', nullable: true),
                                        new OA\Property(property: 'vehicle_id', type: 'integer', nullable: true),
                                        new OA\Property(property: 'delivery_address', type: 'string', nullable: true),
                                        new OA\Property(property: 'recipient_name', type: 'string', nullable: true),
                                        new OA\Property(property: 'recipient_phone', type: 'string', nullable: true),
                                        new OA\Property(property: 'delivered_at', type: 'string', format: 'date-time', nullable: true),
                                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'tracking_history',
                                    type: 'array',
                                    items: new OA\Items(
                                        type: 'object',
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer'),
                                            new OA\Property(property: 'package_id', type: 'integer'),
                                            new OA\Property(property: 'previous_status', type: 'string', nullable: true),
                                            new OA\Property(property: 'new_status', type: 'string'),
                                            new OA\Property(property: 'comment', type: 'string', nullable: true),
                                            new OA\Property(property: 'location', type: 'string', nullable: true),
                                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated - token missing or invalid',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function store(RegisterPackageRequest $request)
    {
        $dto = RegisterPackageDTO::fromArray($request->validated());
        $response = $this->registerPackage->execute($dto);

        return ApiResponse::success($response->toArray()['data'], 'Package registered successfully', 201);
    }

    #[OA\Get(
        path: '/api/v1/packages/{tracking_number}',
        operationId: 'getPackageByTrackingNumber',
        summary: 'Get package details and tracking history',
        description: 'Returns full package information including its complete status history timeline.',
        security: [['bearerAuth' => []]],
        tags: ['Packages'],
        parameters: [
            new OA\Parameter(
                name: 'tracking_number',
                in: 'path',
                required: true,
                description: 'Package tracking number',
                schema: new OA\Schema(type: 'string', example: 'AF-TEST-001')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Package found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Package found'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'package', type: 'object'),
                                new OA\Property(
                                    property: 'tracking_history',
                                    type: 'array',
                                    items: new OA\Items(type: 'object')
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Package not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Package not found: INVALID-999'),
                    ]
                )
            ),
        ]
    )]
    public function show(string $trackingNumber)
    {
        $response = $this->getPackage->execute($trackingNumber);

        return ApiResponse::success($response->toArray()['data'], 'Package found');
    }

    #[OA\Patch(
        path: '/api/v1/packages/{tracking_number}/status',
        operationId: 'updatePackageStatus',
        summary: 'Update package delivery status',
        description: 'Changes the status of a package following valid state transitions. The status flow is: Registered → In Transit → Out for Delivery → Delivered. A package can be Cancelled at any stage except Delivered. Moving to "In Transit" requires an active courier and vehicle.',
        security: [['bearerAuth' => []]],
        tags: ['Packages'],
        parameters: [
            new OA\Parameter(
                name: 'tracking_number',
                in: 'path',
                required: true,
                description: 'Package tracking number',
                schema: new OA\Schema(type: 'string', example: 'AF-TEST-001')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['new_status'],
                properties: [
                    new OA\Property(
                        property: 'new_status',
                        type: 'string',
                        enum: ['In Transit', 'Out for Delivery', 'Delivered', 'Cancelled'],
                        example: 'In Transit',
                        description: 'New status for the package'
                    ),
                    new OA\Property(
                        property: 'comment',
                        type: 'string',
                        maxLength: 255,
                        nullable: true,
                        example: 'En route to destination',
                        description: 'Optional comment about the status change'
                    ),
                    new OA\Property(
                        property: 'location',
                        type: 'string',
                        maxLength: 150,
                        nullable: true,
                        example: 'CDMX Centro',
                        description: 'Current location of the package'
                    ),
                    new OA\Property(
                        property: 'courier_id',
                        type: 'integer',
                        nullable: true,
                        example: 1,
                        description: 'ID of the assigned courier (required for "In Transit" status)'
                    ),
                    new OA\Property(
                        property: 'vehicle_id',
                        type: 'integer',
                        nullable: true,
                        example: 1,
                        description: 'ID of the assigned vehicle (required for "In Transit" status)'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Status updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Status updated successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'package', type: 'object'),
                                new OA\Property(
                                    property: 'tracking_history',
                                    type: 'array',
                                    items: new OA\Items(type: 'object')
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid status transition',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Cannot change from \'Registered\' to \'Delivered\''),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
            new OA\Response(
                response: 404,
                description: 'Package not found',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function updateStatus(string $trackingNumber, UpdatePackageStatusRequest $request)
    {
        $dto = UpdatePackageStatusDTO::fromArray($request->validated());
        $response = $this->updatePackageStatus->execute($trackingNumber, $dto);

        return ApiResponse::success($response->toArray()['data'], 'Status updated successfully');
    }
}

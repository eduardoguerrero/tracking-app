<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Packages',
    description: 'Package registration, tracking, and status management'
)]
class PackageControllerDocs
{
    #[OA\Post(
        path: '/api/v1/packages',
        operationId: 'registerPackage',
        summary: 'Register a new package',
        description: 'Creates a new package with initial "Registered" status.',
        security: [['bearerAuth' => []]],
        tags: ['Packages'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['tracking_number', 'description', 'branch_id'],
                properties: [
                    new OA\Property(
                        property: 'tracking_number', type: 'string', maxLength: 50, example: 'AF-PKG-202501'
                    ),
                    new OA\Property(
                        property: 'description', type: 'string', maxLength: 255, example: 'Dell XPS Laptop'
                    ),
                    new OA\Property(property: 'weight', type: 'number', format: 'float', nullable: true, example: 2.50),
                    new OA\Property(property: 'branch_id', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'delivery_address',
                        type: 'string',
                        maxLength: 255,
                        nullable: true,
                        example: 'Calle Madero 123'
                    ),
                    new OA\Property(
                        property: 'recipient_name',
                        type: 'string',
                        maxLength: 150,
                        nullable: true,
                        example: 'Roberto Sánchez'
                    ),
                    new OA\Property(
                        property: 'recipient_phone',
                        type: 'string',
                        maxLength: 20,
                        nullable: true,
                        example: '55-4000-0001'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201, description: 'Package registered successfully', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Package registered successfully'),
                    new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'package', type: 'object'),
                        new OA\Property(
                            property: 'tracking_history', type: 'array', items: new OA\Items(type: 'object')
                        ),
                    ]),
                ]
            )
            ),
            new OA\Response(
                response: 400, description: 'Validation error', content: new OA\JsonContent(
                ref: '#/components/schemas/ErrorResponse'
            )
            ),
            new OA\Response(
                response: 401, description: 'Unauthenticated', content: new OA\JsonContent(
                ref: '#/components/schemas/ErrorResponse'
            )
            ),
        ]
    )]
    public static function store()
    {
    }

    #[OA\Get(
        path: '/api/v1/packages/{tracking_number}',
        operationId: 'getPackageByTrackingNumber',
        summary: 'Get package details and tracking history',
        description: 'Returns full package information including its status history timeline.',
        security: [['bearerAuth' => []]],
        tags: ['Packages'],
        parameters: [
            new OA\Parameter(
                name: 'tracking_number',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'AF-TEST-001')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200, description: 'Package found', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Package found'),
                    new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'package', type: 'object'),
                        new OA\Property(
                            property: 'tracking_history', type: 'array', items: new OA\Items(type: 'object')
                        ),
                    ]),
                ]
            )
            ),
            new OA\Response(
                response: 401, description: 'Unauthenticated', content: new OA\JsonContent(
                ref: '#/components/schemas/ErrorResponse'
            )
            ),
            new OA\Response(
                response: 404, description: 'Package not found', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(property: 'message', type: 'string', example: 'Package not found: INVALID-999'),
                ]
            )
            ),
        ]
    )]
    public static function show()
    {
    }

    #[OA\Patch(
        path: '/api/v1/packages/{tracking_number}/status',
        operationId: 'updatePackageStatus',
        summary: 'Update package delivery status',
        description: 'Changes package status following valid transitions: Registered → In Transit → Out for Delivery → Delivered.',
        security: [['bearerAuth' => []]],
        tags: ['Packages'],
        parameters: [
            new OA\Parameter(
                name: 'tracking_number',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'AF-TEST-001')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['new_status'],
                properties: [
                    new OA\Property(property: 'new_status', type: 'string', enum: [
                        'In Transit',
                        'Out for Delivery',
                        'Delivered',
                        'Cancelled'
                    ], example: 'In Transit'),
                    new OA\Property(
                        property: 'comment',
                        type: 'string',
                        maxLength: 255,
                        nullable: true,
                        example: 'En route'
                    ),
                    new OA\Property(
                        property: 'location',
                        type: 'string',
                        maxLength: 150,
                        nullable: true,
                        example: 'CDMX Centro'
                    ),
                    new OA\Property(property: 'courier_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'vehicle_id', type: 'integer', nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, description: 'Status updated successfully', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: true),
                    new OA\Property(property: 'message', type: 'string', example: 'Status updated successfully'),
                    new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'package', type: 'object'),
                        new OA\Property(
                            property: 'tracking_history', type: 'array', items: new OA\Items(type: 'object')
                        ),
                    ]),
                ]
            )
            ),
            new OA\Response(
                response: 400, description: 'Invalid status transition', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'success', type: 'boolean', example: false),
                    new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: "Cannot change from 'Registered' to 'Delivered'"
                    ),
                ]
            )
            ),
            new OA\Response(
                response: 401, description: 'Unauthenticated', content: new OA\JsonContent(
                ref: '#/components/schemas/ErrorResponse'
            )
            ),
            new OA\Response(
                response: 404, description: 'Package not found', content: new OA\JsonContent(
                ref: '#/components/schemas/ErrorResponse'
            )
            ),
        ]
    )]
    public static function updateStatus()
    {
    }
}

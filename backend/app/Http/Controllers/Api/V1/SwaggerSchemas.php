<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ErrorResponse',
    description: 'Standardized error response',
)]
class SwaggerSchemas
{
    #[OA\Property(property: 'success', type: 'boolean', example: false)]
    public bool $success;

    #[OA\Property(property: 'message', type: 'string', example: 'Error description')]
    public string $message;

    #[OA\Property(
        property: 'errors',
        type: 'object',
        nullable: true,
        additionalProperties: new OA\AdditionalProperties(
            type: 'array',
            items: new OA\Items(type: 'string')
        ),
        example: '{"tracking_number": ["The tracking_number field is required."]}'
    )]
    public ?object $errors;
}

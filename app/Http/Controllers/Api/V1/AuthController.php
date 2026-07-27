<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Infrastructure\Auth\JwtAuthService;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Aeroflash Tracking API',
    description: 'REST API for package tracking and shipment status management. This API allows registering packages, querying tracking information, and updating delivery status.',
)]
#[OA\Server(
    url: 'http://localhost:8090',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    description: 'Enter JWT token obtained from /api/v1/auth/login',
    scheme: 'bearer',
    bearerFormat: 'JWT',
)]
class AuthController extends Controller
{
    public function __construct(
        private readonly JwtAuthService $jwtAuthService,
    ) {
    }

    #[OA\Post(
        path: '/api/v1/auth/login',
        operationId: 'login',
        summary: 'Authenticate and obtain JWT token',
        description: 'Validates user credentials and returns a JWT access token for authenticating subsequent requests.',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'admin@aeroflash.com'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        format: 'password',
                        example: 'password'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authentication successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Authentication successful'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'access_token', type: 'string', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'),
                                new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                                new OA\Property(property: 'expires_in', type: 'integer', example: 3600),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Validation error'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid credentials'),
                    ]
                )
            ),
        ]
    )]
    public function login(LoginRequest $request)
    {
        $token = $this->jwtAuthService->attempt(
            $request->input('email'),
            $request->input('password'),
        );

        if (!$token) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        return ApiResponse::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl', 3600),
        ], 'Authentication successful');
    }
}

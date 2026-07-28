<?php

declare(strict_types=1);

namespace App\Application\Responses;

class AuthResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?array $data = null,
    ) {
    }

    public static function fromToken(string $token, ?int $ttl = null): self
    {
        return new self(
            success: true,
            message: 'Authentication successful',
            data: [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => $ttl ?? (int)config('jwt.ttl', 3600),
            ],
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}

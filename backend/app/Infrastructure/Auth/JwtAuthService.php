<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JwtAuthService
{
    private string $secret;
    private string $algorithm;
    private int $ttl;
    private int $refreshTtl;

    public function __construct()
    {
        $this->secret = config('jwt.secret') ?: config('app.key');

        if (empty($this->secret)) {
            throw new \RuntimeException(
                'JWT_SECRET is not set. Define it in .env or docker-compose environment.'
            );
        }

        $this->algorithm = config('jwt.algorithm', 'HS256');
        $this->ttl = (int) config('jwt.ttl', 3600);
        $this->refreshTtl = (int) config('jwt.refresh_ttl', 1209600);
    }

    public function attempt(string $email, string $password): ?string
    {
        $normalizedEmail = $this->normalizeEmail($email);

        $user = User::where('email', $normalizedEmail)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $this->generateToken($user);
    }

    public function generateToken(User $user): string
    {
        $now = time();
        $payload = [
            'iss' => config('app.url'),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $this->ttl,
            'sub' => $user->id,
            'jti' => Str::uuid()->toString(),
        ];

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function validateToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algorithm));
        } catch (\Exception) {
            return null;
        }
    }

    public function getUserFromToken(string $token): ?User
    {
        $payload = $this->validateToken($token);

        if (!$payload || !isset($payload->sub)) {
            return null;
        }

        return User::find($payload->sub);
    }

    private function normalizeEmail(string $email): string
    {
        return trim(mb_strtolower($email));
    }

    public function refreshToken(string $token): ?string
    {
        $payload = $this->validateExpiredToken($token);

        if (!$payload || !isset($payload->sub)) {
            return null;
        }

        $user = User::find($payload->sub);

        if (!$user) {
            return null;
        }

        return $this->generateToken($user);
    }

    private function validateExpiredToken(string $token): ?object
    {
        try {
            $payload = JWT::decode(
                $token,
                new Key($this->secret, $this->algorithm)
            );
        } catch (\Exception) {
            return null;
        }

        if (!isset($payload->sub, $payload->iat)) {
            return null;
        }

        if (time() > $payload->iat + $this->refreshTtl) {
            return null;
        }

        return $payload;
    }
}

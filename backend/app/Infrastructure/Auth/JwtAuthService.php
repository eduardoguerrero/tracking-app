<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Models\User;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
            throw new \RuntimeException('JWT_SECRET is not set. Define it in .env or docker-compose environment.');
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
        } catch (ExpiredException $e) {
            Log::warning('JWT token expired', ['sub' => $this->extractSub($token)]);
            return null;
        } catch (SignatureInvalidException $e) {
            Log::warning('JWT signature invalid');
            return null;
        } catch (\UnexpectedValueException $e) {
            Log::warning('JWT validation failed', ['error' => $e->getMessage()]);
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

    /**
     * Validates a potentially expired JWT for the refresh flow.
     *
     * Extracts the payload manually to check the refresh window (iat + refreshTtl),
     * then verifies the full token signature. Returns the payload if:
     *   - Signature is valid (even if exp has passed)
     *   - Token is within the refresh window (iat + refreshTtl >= now)
     */
    private function validateExpiredToken(string $token): ?object
    {
        $payload = $this->extractPayload($token);

        if (!$payload || !isset($payload->sub, $payload->iat)) {
            return null;
        }

        if (time() > $payload->iat + $this->refreshTtl) {
            Log::info('JWT refresh rejected: outside refresh window');
            return null;
        }

        try {
            JWT::decode($token, new Key($this->secret, $this->algorithm));
            return $payload;
        } catch (ExpiredException) {
            return $payload;
        } catch (SignatureInvalidException $e) {
            Log::warning('JWT refresh rejected: invalid signature');
            return null;
        } catch (\UnexpectedValueException $e) {
            Log::warning('JWT refresh failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function extractPayload(string $token): ?object
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        try {
            return json_decode(JWT::urlsafeB64Decode($parts[1]),false);
        } catch (\UnexpectedValueException) {
            return null;
        }
    }

    private function extractSub(string $token): ?int
    {
        $payload = $this->extractPayload($token);

        return $payload->sub ?? null;
    }

    private function normalizeEmail(string $email): string
    {
        return trim(mb_strtolower($email));
    }

    public function blacklist(string $token): void
    {
        $payload = $this->extractPayload($token);

        if ($payload && isset($payload->jti, $payload->exp)) {
            $ttl = max(0, (int) $payload->exp - time());
            Cache::put('jwt_blacklist:' . $payload->jti, true, $ttl);
        }
    }

    public function isBlacklisted(string $token): bool
    {
        $payload = $this->extractPayload($token);

        if ($payload && isset($payload->jti)) {
            return Cache::has('jwt_blacklist:' . $payload->jti);
        }

        return false;
    }
}

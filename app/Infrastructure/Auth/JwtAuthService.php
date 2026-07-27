<?php

namespace App\Infrastructure\Auth;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;

class JwtAuthService
{
    private string $secret;
    private string $algorithm;
    private int $ttl;

    public function __construct()
    {
        $this->secret = config('jwt.secret', config('app.key'));
        $this->algorithm = config('jwt.algorithm', 'HS256');
        $this->ttl = config('jwt.ttl', 3600);
    }

    public function attempt(string $email, string $password): ?string
    {
        $user = User::where('email', $email)->first();

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
            'exp' => $now + $this->ttl,
            'sub' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
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
}

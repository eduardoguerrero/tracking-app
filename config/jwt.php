<?php

return [
    'secret' => env('JWT_SECRET', 'default-jwt-secret-change-in-production'),
    'ttl' => env('JWT_TTL', 3600),
    'algorithm' => 'HS256',
];

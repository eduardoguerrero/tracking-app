<?php

return [
    'secret' => env('JWT_SECRET', 'default-jwt-secret-change-in-production'),
    'ttl' => env('JWT_TTL', 3600),
    'refresh_ttl' => env('JWT_REFRESH_TTL', 1209600),
    'algorithm' => 'HS256',
];

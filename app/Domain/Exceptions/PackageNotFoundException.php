<?php

namespace App\Domain\Exceptions;

class PackageNotFoundException extends \RuntimeException
{
    public function __construct(string $trackingNumber)
    {
        parent::__construct("Package not found: {$trackingNumber}", 404);
    }
}

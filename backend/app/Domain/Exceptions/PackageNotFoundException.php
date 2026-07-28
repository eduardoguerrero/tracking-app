<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use Illuminate\Http\Response;

class PackageNotFoundException extends \RuntimeException
{
    public function __construct(string $trackingNumber)
    {
        parent::__construct("Package not found", Response::HTTP_NOT_FOUND);
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use Illuminate\Http\Response;

class InvalidStatusTransitionException extends \RuntimeException
{
    public readonly int $httpCode;

    public function __construct(string $currentStatus, string $newStatus, ?string $additionalMessage = null)
    {
        $message = "Cannot change from '{$currentStatus}' to '{$newStatus}'";

        if ($additionalMessage) {
            $message .= ": {$additionalMessage}";
        }

        parent::__construct($message);
        $this->httpCode = Response::HTTP_BAD_REQUEST;
    }
}

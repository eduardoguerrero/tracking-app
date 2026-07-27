<?php

namespace App\Application\Responses;

use App\Domain\Entities\Package;
use App\Domain\Entities\TrackingHistory;

class PackageResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?array $data = null,
    ) {
    }

    public static function fromPackage(Package $package, array $history = []): self
    {
        return new self(
            success: true,
            message: 'Operation successful',
            data: [
                'package' => $package->toArray(),
                'tracking_history' => array_map(fn(TrackingHistory $h) => $h->toArray(), $history),
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

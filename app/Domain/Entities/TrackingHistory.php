<?php

namespace App\Domain\Entities;

class TrackingHistory
{
    public function __construct(
        public readonly int $packageId,
        public readonly ?string $previousStatus,
        public readonly string $newStatus,
        public readonly ?string $comment = null,
        public readonly ?string $location = null,
        public readonly ?int $id = null,
        public readonly ?string $createdAt = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'package_id' => $this->packageId,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'comment' => $this->comment,
            'location' => $this->location,
            'created_at' => $this->createdAt,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum PackageStatusEnum: string
{
    case Registered = 'Registered';
    case InTransit = 'In Transit';
    case OutForDelivery = 'Out for Delivery';
    case Delivered = 'Delivered';
    case Cancelled = 'Cancelled';

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Registered => [self::InTransit, self::Cancelled],
            self::InTransit => [self::OutForDelivery, self::Cancelled],
            self::OutForDelivery => [self::Delivered, self::Cancelled],
            self::Delivered => [],
            self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions(), true);
    }

    public function requiresAssignedCourierAndVehicle(): bool
    {
        return $this === self::InTransit;
    }

    public static function fromString(string $status): self
    {
        return match ($status) {
            'Registered' => self::Registered,
            'In Transit' => self::InTransit,
            'Out for Delivery' => self::OutForDelivery,
            'Delivered' => self::Delivered,
            'Cancelled' => self::Cancelled,
            default => throw new \InvalidArgumentException("Invalid status: {$status}"),
        };
    }
}

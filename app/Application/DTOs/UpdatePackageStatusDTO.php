<?php

namespace App\Application\DTOs;

use App\Domain\Enums\PackageStatusEnum;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class UpdatePackageStatusDTO
{
    public function __construct(
        public readonly PackageStatusEnum $newStatus,
        public readonly ?string $comment = null,
        public readonly ?string $location = null,
        public readonly ?int $courierId = null,
        public readonly ?int $vehicleId = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $validator = Validator::make($data, [
            'new_status' => ['required', 'string', 'max:50'],
            'comment' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:150'],
            'courier_id' => ['nullable', 'integer', 'exists:couriers,id'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException(
                json_encode($validator->errors()->toArray())
            );
        }

        try {
            $newStatus = PackageStatusEnum::fromString($data['new_status']);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidArgumentException(
                json_encode(['new_status' => ['Invalid status']])
            );
        }

        return new self(
            newStatus: $newStatus,
            comment: $data['comment'] ?? null,
            location: $data['location'] ?? null,
            courierId: isset($data['courier_id']) ? (int) $data['courier_id'] : null,
            vehicleId: isset($data['vehicle_id']) ? (int) $data['vehicle_id'] : null,
        );
    }
}

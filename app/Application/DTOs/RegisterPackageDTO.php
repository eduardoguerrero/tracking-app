<?php

namespace App\Application\DTOs;

use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class RegisterPackageDTO
{
    public function __construct(
        public readonly string $trackingNumber,
        public readonly string $description,
        public readonly ?float $weight,
        public readonly int $branchId,
        public readonly ?string $deliveryAddress = null,
        public readonly ?string $recipientName = null,
        public readonly ?string $recipientPhone = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $validator = Validator::make($data, [
            'tracking_number' => ['required', 'string', 'max:50', 'unique:packages,tracking_number'],
            'description' => ['required', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric', 'min:0.01', 'max:9999.99'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'recipient_phone' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException(
                json_encode($validator->errors()->toArray())
            );
        }

        return new self(
            trackingNumber: $data['tracking_number'],
            description: $data['description'],
            weight: isset($data['weight']) ? (float) $data['weight'] : null,
            branchId: (int) $data['branch_id'],
            deliveryAddress: $data['delivery_address'] ?? null,
            recipientName: $data['recipient_name'] ?? null,
            recipientPhone: $data['recipient_phone'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'tracking_number' => $this->trackingNumber,
            'description' => $this->description,
            'weight' => $this->weight,
            'branch_id' => $this->branchId,
            'delivery_address' => $this->deliveryAddress,
            'recipient_name' => $this->recipientName,
            'recipient_phone' => $this->recipientPhone,
        ], fn($value) => $value !== null);
    }
}

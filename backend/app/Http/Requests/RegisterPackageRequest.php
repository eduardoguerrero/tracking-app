<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Response;

class RegisterPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tracking_number' => ['required', 'string', 'max:50', 'unique:packages,tracking_number'],
            'description' => ['required', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric', 'min:0.01', 'max:9999.99'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'recipient_phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('Validation error', Response::HTTP_BAD_REQUEST, $validator->errors()->toArray())
        );
    }
}

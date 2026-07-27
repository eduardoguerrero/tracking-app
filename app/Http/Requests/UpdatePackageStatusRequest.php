<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Responses\ApiResponse;

class UpdatePackageStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_status' => ['required', 'string', 'max:50'],
            'comment' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:150'],
            'courier_id' => ['nullable', 'integer', 'exists:couriers,id'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('Validation error', 400, $validator->errors()->toArray())
        );
    }
}

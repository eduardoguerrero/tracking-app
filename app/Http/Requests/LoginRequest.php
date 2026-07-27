<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Response;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    /**
     * OWASP: Input Sanitization
     * - email:rfc,dns — validates RFC 5321 format, checks domain has MX/A records.
     * - max:100 — prevents buffer/truncation attacks on email field.
     * - min:6 — minimum password length, adjusted by BCRYPT_ROUNDS (12).
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => trim(mb_strtolower($this->input('email')))]);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error('Validation error', Response::HTTP_BAD_REQUEST, $validator->errors()->toArray())
        );
    }
}

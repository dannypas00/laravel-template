<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @codeCoverageIgnore It's just validation rules
 */
class BaseUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string',
            'email'    => 'required|email',
            // On non-production environments, password can be any string
            'password' => app()->environment('production')
                ? Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->letters()
                    ->uncompromised()
                    ->sometimes()
                : 'string|sometimes',
        ];
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mail_address' => [
                'required',
                'string',
                'email',
                'unique:users',
                'max:' . config('length.max_string'),
            ],
            'password' => [
                'required',
                'string',
                'min:' . config('length.min_string'),
                'max:' . config('length.max_string'),
                'confirmed',
                'regex:' . config('regex.password'),
            ],
        ];
    }
}

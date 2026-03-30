<?php

namespace App\Http\Requests\Admin\Contact;

use Illuminate\Foundation\Http\FormRequest;

class CreateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:' . config('length.max_string')],
            'email'     => ['required', 'email', 'max:' . config('length.max_string')],
            'phone'     => ['nullable', 'string', 'regex:' . config('regex.telephone')],
            'title'     => ['required', 'string', 'max:' . config('length.max_string')],
            'content'   => ['required', 'string'],
        ];
    }
}

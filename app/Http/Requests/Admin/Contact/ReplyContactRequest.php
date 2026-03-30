<?php

namespace App\Http\Requests\Admin\Contact;

use Illuminate\Foundation\Http\FormRequest;

class ReplyContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mail_template_id' => ['required', 'integer', 'exists:mail_templates,id'],
            'subject'          => ['sometimes', 'string', 'max:' . config('length.max_string')],
            'body'             => ['sometimes', 'string'],
        ];
    }
}

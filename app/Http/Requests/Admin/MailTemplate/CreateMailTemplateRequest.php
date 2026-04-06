<?php

namespace App\Http\Requests\Admin\MailTemplate;

use App\Models\MailTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:' . config('length.max_string')],
            'code'      => ['required', 'string', 'unique:mail_templates,code'],
            'subject'   => ['required', 'string', 'max:' . config('length.max_string')],
            'body'      => ['required', 'string'],
            'variables' => ['nullable', 'array'],
            'variables.*' => ['string'],
            'type'      => ['required', Rule::in(MailTemplate::TEMPLATE_TYPES)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

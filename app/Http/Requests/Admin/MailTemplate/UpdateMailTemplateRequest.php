<?php

namespace App\Http\Requests\Admin\MailTemplate;

use App\Models\MailTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name'      => ['sometimes', 'string', 'max:' . config('length.max_string')],
            'code'      => ['sometimes', 'string', Rule::unique('mail_templates', 'code')->ignore($id)],
            'subject'   => ['sometimes', 'string', 'max:' . config('length.max_string')],
            'body'      => ['sometimes', 'string'],
            'variables' => ['sometimes', 'nullable', 'array'],
            'variables.*' => ['string'],
            'type'      => ['sometimes', Rule::in(MailTemplate::TEMPLATE_TYPES)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}

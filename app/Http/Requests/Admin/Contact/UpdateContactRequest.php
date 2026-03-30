<?php

namespace App\Http\Requests\Admin\Contact;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'     => ['sometimes', Rule::in(Contact::CONTACT_STATUSES)],
            'priority'   => ['sometimes', Rule::in(Contact::CONTACT_PRIORITIES)],
            'admin_note' => ['sometimes', 'nullable', 'string'],
        ];
    }
}

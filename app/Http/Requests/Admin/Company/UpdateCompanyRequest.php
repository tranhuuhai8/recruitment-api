<?php

namespace App\Http\Requests\Admin\Company;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:' . config('length.max_string'),
                'unique:companies,name,' . $this->id,
            ],
            'short_name' => [
                'required',
                'string',
                'max:' . config('length.max_short_name'),
                'unique:companies,short_name,' . $this->id,
            ],
            'mail_address' => [
                'required',
                'string',
                'email',
                'max:' . config('length.max_string'),
            ],
            'telephone' => [
                'required',
                'string',
                'regex:' . config('regex.telephone'),
                'unique:companies,telephone,' . $this->id,
            ],
            'address' => [
                'required',
                'string',
                'max:' . config('length.max_string'),
            ],
            'website' => [
                'string',
                'max:' . config('length.max_string'),
            ],
            'status' => Rule::in([User::STATUS_ACTIVE, User::STATUS_INACTIVE])
        ];
    }
}

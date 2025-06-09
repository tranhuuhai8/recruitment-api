<?php

namespace App\Http\Requests\Admin\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantRequest extends FormRequest
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
            ],
            'mail_address' => [
                'required',
                'string',
                'email',
                'max:' . config('length.max_string'),
            ],
            'gender' => [
                'required',
                'integer',
                'max:' . config('length.max_string'),
            ],
            'birthday' => [
                'required',
                'date_format:Y-m-d',
                'before:today',
            ],
            'telephone' => [
                'required',
                'string',
                'regex:' . config('regex.telephone'),
                'unique:applicants,telephone,' . $this->id,
                // Rule::unique('area_groups', 'name')->withoutTrashed()->ignore($this->id)
            ],
            'address' => [
                'required',
                'string',
                'max:' . config('length.max_string'),
            ],
            'description' => [
                'max:' . config('length.max_string'),
            ],
        ];
    }
}

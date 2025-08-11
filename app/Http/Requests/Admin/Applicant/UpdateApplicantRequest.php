<?php

namespace App\Http\Requests\Admin\Applicant;

use App\Models\Applicant;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                Rule::unique('users')->ignore($this->id)->whereNull('deleted_at'),
            ],
            'status' => Rule::in([User::STATUS_ACTIVE, User::STATUS_UNVERIFIED, User::STATUS_LOCKED]),
            'gender' => Rule::in([Applicant::GENDER_MALE, Applicant::GENDER_FEMALE, Applicant::GENDER_OTHER]),
            'birthday' => [
                'required',
                'date_format:Y-m-d',
                'before:today',
            ],
            'telephone' => [
                'required',
                'string',
                'regex:' . config('regex.telephone'),
                'unique:applicants,telephone,' . $this->id . ',user_id',
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

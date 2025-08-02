<?php

namespace App\Http\Requests\Applicant\Information;

use App\Models\Applicant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInfoRequest extends FormRequest
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
        $applicant = Applicant::where('user_id', auth('applicant')->id())->first();
        $telephoneRule = Rule::unique('applicants', 'telephone');
        if ($applicant) {
            $telephoneRule->ignore($applicant->id);
        }

        return [
            'name' => [
                'required',
                'string',
                'max:' . config('length.max_string'),
            ],
            'avatar' => [
                'string',
                'max:' . config('length.max_string'),
            ],
            'mail_address' => [
                'required',
                'string',
                'email',
                'max:' . config('length.max_string'),
                'unique:users,mail_address,' . auth(guard: 'applicant')->id() . ',id'
            ],
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
                // Rule::unique('applicants', 'telephone')->withoutTrashed()->ignore(auth('applicant')->id())
                $telephoneRule
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

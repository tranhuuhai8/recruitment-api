<?php

namespace App\Http\Requests\Home\Job;

use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplyJobRequest extends FormRequest
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
            'applicant_id' => [
                'nullable',
                'exists:applicants,id'
            ],
            'job_id' => [
                'required',
                'exists:jobs,id'
            ],
            'application_file_id' => [
                'nullable',
                'exists:application_file,id'
            ],
            'file_name' => [
                'required_without:application_file_id',
                'string',
                'max:' . config('length.max_string'),
            ],
            'file_path' => [
                'required_without:application_file_id',
                'string',
                'max:' . config('length.max_string'),
            ],
            'guest_name' => [
                'string',
                'max:' . config('length.max_string'),
            ],
            'guest_telephone' => [
                'string',
                'regex:' . config('regex.telephone'),
            ],
            'guest_email' => [
                'string',
                'email',
                'max:' . config('length.max_string'),
            ],
            'cover_letter' => 'required',
            'status' => Rule::in(JobApplication::STATUSES),
        ];
    }
}

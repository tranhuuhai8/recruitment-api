<?php

namespace App\Http\Requests\Company\Job;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:' . config('length.max_string'),
            ],
            'description' => ['required'],
            'number_of_recruitment' => ['integer', 'nullable'],
            'salary_min' => ['integer', 'nullable'],
            'salary_max' => [
                'integer',
                'nullable',
                function ($attr, $value, $fail) {
                    $min = $this->input('salary_min');
                    if (!is_null($min) && !is_null($value) && $value <= $min) {
                        $fail('Lương tối đa phải lớn hơn lương tối thiểu.');
                    }
                },
            ],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => [
                'required',
                'date_format:Y-m-d',
                'after:start_date',
            ],
            'city_id' => ['required', 'exists:cities,id',],
            'job_category_id' => ['required', 'exists:job_categories,id',],
            'salary_type' => Rule::in([Job::SALARY_TYPE_NET, Job::SALARY_TYPE_GROSS, Job::SALARY_TYPE_EXCHANGE]),
            'status' => Rule::in([Job::STATUS_DRAFT, Job::STATUS_OPEN, Job::STATUS_CLOSED]),
            'type' => Rule::in([Job::TYPE_FULLTIME, Job::TYPE_PART_TIME])
        ];
    }
}

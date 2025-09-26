<?php

namespace App\Http\Requests\Admin\JobCategory;

use App\Models\JobCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateJobCategoryRequest extends FormRequest
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
                'regex:' . config('regex.no_special_chars'),
            ],
            'description' => [
                'string',
                'max:' . config('length.max_string'),
                'regex:' . config('regex.no_special_chars_des'),
            ],
            'status' => Rule::in([JobCategory::STATUS_SHOW, JobCategory::STATUS_HIDE]),
            'type' => Rule::in([JobCategory::TYPE_DEFAULT, JobCategory::TYPE_CUSTOMIZE]),
            'parent_id' => 'nullable|exists:job_categories,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Ngành nghề',
        ];
    }
}

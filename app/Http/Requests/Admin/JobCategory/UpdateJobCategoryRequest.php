<?php

namespace App\Http\Requests\Admin\JobCategory;

use App\Models\JobCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobCategoryRequest extends FormRequest
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
            'description' => [
                'string',
                'max:' . config('length.max_string'),
            ],
            'status' => Rule::in([JobCategory::STATUS_SHOW, JobCategory::STATUS_HIDE]),
            'type' => Rule::in([JobCategory::TYPE_DEFAULT, JobCategory::TYPE_CUSTOMIZE]),
            'parent_id' => 'nullable|exist:job_categories.id',
        ];
    }
}

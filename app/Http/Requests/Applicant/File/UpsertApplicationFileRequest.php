<?php

namespace App\Http\Requests\Applicant\File;

use Illuminate\Foundation\Http\FormRequest;

class UpsertApplicationFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Method validationData
     *
     * @return array
     */
    public function validationData(): array
    {
        return ['files' => $this->all()];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'max:' . config('length.max_file_upload')],
            'files.*.file_name' => [
                'required',
                'string',
                'max:' . config('length.max_file_name'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'files.max' => trans('validation.max.total_file_upload', ['max' => config('length.max_file_upload')]),
            'files.*.file_name.required' => 'Tên file là bắt buộc.',
            'files.*.file_name.max' => 'Tên file là tối đa ' . config('length.max_file_name') . ' ký tự.',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin\City;

use App\Models\City;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCityRequest extends FormRequest
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
            ],
            'status' => Rule::in([City::STATUS_SHOW, City::STATUS_HIDE]),
            'parent_id' => 'nullable|exists:cities,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Khu vực',
        ];
    }
}

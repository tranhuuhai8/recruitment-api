<?php

namespace App\Http\Requests\Company\Information;

use App\Models\Company;
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
        $company = Company::where('user_id', auth('company')->id())->first();
        $telephoneRule = Rule::unique('companies', 'telephone');
        $nameRule = Rule::unique('companies', 'name');
        $shortNameRule = Rule::unique('companies', 'short_name');
        if ($company) {
            $telephoneRule->ignore($company->id);
            $nameRule->ignore($company->id);
            $shortNameRule->ignore($company->id);
        }

        return [
            'name' => [
                'required',
                'string',
                'max:' . config('length.max_string'),
                $nameRule,
                'regex:' . config('regex.no_special_chars'),
            ],
            'short_name' => [
                'required',
                'string',
                'max:' . config('length.max_short_name'),
                $shortNameRule,
                'regex:' . config('regex.no_special_chars'),
            ],
            'mail_address' => [
                'required',
                'string',
                'email',
                'max:' . config('length.max_string'),
                'unique:users,mail_address,' . auth('company')->id() . ',id,deleted_at,NULL',
            ],
            'telephone' => [
                'required',
                'string',
                'regex:' . config('regex.telephone'),
                $telephoneRule
            ],
            'city_id' => [
                'required',
                'exists:cities,id',
            ],
            'address' => [
                'required',
                'string',
                'max:' . config('length.max_string'),
                'regex:' . config('regex.no_special_chars_des'),
            ],
            'website' => [
                'max:' . config('length.max_string'),
                'regex:' . config('regex.website'),
            ],
        ];
    }
}

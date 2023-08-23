<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "personal_info.code" => [
                "required",
                $this->route()->id
                    ? "unique:users,account_code," . $this->route()->id
                    : "unique:users,account_code",
            ],
            "personal_info.first" => "required",
            "personal_info.last" => "required",
            "location.name" => "required",
            "department.name" => "required",
            "company.name" => "required",
            "role_id" => "required|exists:role,id,deleted_at,NULL",
        ];
    }
    public function attributes()
    {
        return [
            "personal_info.code" => "account code",
            "personal_info.first" => "first name",
            "personal_info.last" => "last name",
            "location.name" => "location",
            "department.name" => "department",
            "company.name" => "company",
        ];
    }

    public function messages()
    {
        return [
            "unique" => "The :attribute field is already been taken.",
            "required" => "The :attribute field is required.",
        ];
    }
}

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
            "personal_info.id_no" => [
                "required",
                $this->route()->id
                    ? "unique:users,id_no," . $this->route()->id
                    : "unique:users,id_no",
            ],
            "personal_info.first" => "required",
            "personal_info.last" => "required",
            "location" => "required",
            "department" => "required",
            "company" => "required",
            // "role_id" => "required|exists:role,id,deleted_at,NULL",
        ];
    }
    public function attributes()
    {
        return [
            "personal_info.id_no" => "Id no",
            "personal_info.first" => "first name",
            "personal_info.last" => "last name",
            "location" => "location",
            "department" => "department",
            "company" => "company",
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

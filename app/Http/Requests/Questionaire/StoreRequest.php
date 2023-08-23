<?php

namespace App\Http\Requests\Questionaire;

use Illuminate\Foundation\Http\FormRequest;

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
            "question" => [
                "required",
                $this->route()->question
                    ? "unique:questionaire,question," . $this->route()->question
                    : "unique:questionaire,question",
            ],
            "type" => ["required"],
            "answers" => [
                "exists:answers,id,deleted_at,NULL",
                "requiredIf:type,choice",
            ],
            "answers.*" => "numeric",
        ];
    }
}

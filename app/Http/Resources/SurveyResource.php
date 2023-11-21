<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id_prefix" => $this->id_prefix,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "last_name" => $this->last_name,
            "sex" => $this->sex,
            "company_name" => $this->company_name,
            "department_name" => $this->department_name,
            "location_name" => $this->location_name,
            "survey" => $this->survey,
        ];
    }
}

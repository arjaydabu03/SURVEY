<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class UserFilter extends QueryFilters
{
    protected array $allowedFilters = [
        "id_prefix",
        "id_no",
        "first_name",
        "middle_name",
        "last_name",
        "sex",
        "location_name",
        "department_name",
        "company_name",
        "created_at",
        "updated_at",
        "deleted_at",
    ];
    protected array $allowedSorts = ["updated_at"];

    protected array $columnSearch = [
        "id_prefix",
        "id_no",
        "first_name",
        "middle_name",
        "last_name",
        "sex",
        "location_name",
        "department_name",
        "company_name",
    ];
    public function from($from)
    {
        $this->builder->whereDate("updated_at", ">=", $from);
    }
    public function to($to)
    {
        $this->builder->whereDate("updated_at", "<=", $to);
    }
}

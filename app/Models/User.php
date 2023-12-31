<?php

namespace App\Models;

use App\Filters\UserFilter;
use Laravel\Sanctum\HasApiTokens;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "id_prefix",
        "id_no",
        "first_name",
        "middle_name",
        "last_name",
        "sex",
        "role_id",
        "location_name",
        "department_name",
        "company_name",
    ];
    protected $hidden = ["remember_token", "role_id"];

    protected string $default_filters = UserFilter::class;

    function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function question()
    {
        return $this->belongsTo(Questionaire::class);
    }
    public function survey()
    {
        return $this->hasMany(Survey::class, "user_id", "id");
    }
}

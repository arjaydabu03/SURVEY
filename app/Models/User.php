<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
    protected $hidden = [
        "remember_token",
        "created_at",
        "deleted_at",
        "role_id",
    ];

    function role()
    {
        return $this->belongsTo(Role::class);
    }
}

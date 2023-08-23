<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "survey";

    protected $fillable = ["user_id", "question_id", "answer"];

    protected $hidden = [
        "created_at",
        "deleted_at",
        "question_id",
        "user_id",
        "updated_at",
    ];

    public function question()
    {
        return $this->belongsTo(Questionaire::class, "id");
    }
    public function user()
    {
        return $this->belongsTo(User::class, "id");
    }
}

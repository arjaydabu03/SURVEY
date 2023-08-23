<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "questionaire";

    protected $fillable = ["question", "type"];

    protected $hidden = ["created_at", "deleted_at"];

    public function answers()
    {
        return $this->belongsToMany(
            Answer::class,
            "questionaire_answers",
            "question_id",
            "answer_id",
            "id",
            "id"
        );
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("questionaire_answers", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("question_id")->index();
            $table
                ->foreign("question_id")
                ->references("id")
                ->on("questionaire");
            $table->unsignedInteger("answer_id")->index();
            $table
                ->foreign("answer_id")
                ->references("id")
                ->on("answers");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("questionaire_answers");
    }
};

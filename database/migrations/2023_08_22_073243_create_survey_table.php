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
        Schema::create("survey", function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("user_id")->index();
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table->unsignedInteger("question_id")->index();
            $table
                ->foreign("question_id")
                ->references("id")
                ->on("questionaire");
            $table->string("answer");
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
        Schema::dropIfExists("survey");
    }
};

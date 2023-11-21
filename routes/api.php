<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\SurveyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::post("logout", [UserController::class, "logout"]);

    Route::patch("role/{id}", [RoleController::class, "destroy"]);
    Route::apiResource("role", RoleController::class);

    Route::patch("question/{id}", [QuestionController::class, "destroy"]);
    Route::apiResource("question", QuestionController::class);

    Route::patch("answer/{id}", [AnswerController::class, "destroy"]);
    Route::apiResource("answer", AnswerController::class);

    Route::patch("survey/{id}", [SurveyController::class, "destroy"]);
    Route::apiResource("survey", SurveyController::class);
});
Route::patch("user/{id}", [UserController::class, "destroy"]);
Route::post("user/import", [UserController::class, "import_user"]);
Route::apiResource("user", UserController::class);

Route::post("login", [UserController::class, "login"]);

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Functions\GlobalFunction;
use App\Response\Message;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\Questionaire\DisplayRequest;

use Essa\APIToolKit\Api\ApiResponse;

class UserController extends Controller
{
    use ApiResponse;

    public function index(DisplayRequest $request)
    {
        $status = $request->status;
        $date = $request->date;
        $users = User::with("role")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->UseFilters()
            ->dynamicPaginate();

        $is_empty = $users->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        return GlobalFunction::response_function(Message::USER_DISPLAY, $users);
    }

    public function store(StoreRequest $request)
    {
        $users = User::create([
            "id_prefix" => $request["personal_info"]["id_prefix"],
            "id_no" => $request["personal_info"]["id_no"],
            "first_name" => $request["personal_info"]["first"],
            "middle_name" => $request["personal_info"]["middle"],
            "last_name" => $request["personal_info"]["last"],
            "sex" => $request["personal_info"]["sex"],
            "role_id" => $request["role_id"],
            "location_name" => $request["location"],
            "department_name" => $request["department"],
            "company_name" => $request["company"],
        ]);
        return GlobalFunction::save(Message::USER_SAVE, $users);
    }
    public function login(Request $request)
    {
        $user = User::with("role")
            ->where("id_prefix", $request->id_prefix)
            ->where("id_no", $request->id_no)
            ->first();

        if (
            !$user ||
            ($request->id_prefix &&
                ($request->id_prefix = !$user->id_prefix && !$user->id_no))
        ) {
            throw ValidationException::withMessages([
                "id_prefix" => ["The provided credentials are incorrect."],
                "id_no" => ["The provided credentials are incorrect."],
            ]);

            if ($user || $request->id_no == $user->id_no) {
                return GlobalFunction::response_function(
                    Message::INVALID_ACTION
                );
            }
        }
        // return "success";
        $token = $user->createToken("PersonalAccessToken")->plainTextToken;
        $user["token"] = $token;

        $cookie = cookie("survey_coockie", $token);

        return GlobalFunction::response_function(
            Message::LOGIN_USER,
            $user
        )->withCookie($cookie);
    }
    public function logout(Request $request)
    {
        // auth()->user()->tokens()->delete();//all token of one user
        auth()
            ->user()
            ->currentAccessToken()
            ->delete(); //current user
        return GlobalFunction::response_function(Message::LOGOUT_USER);
    }
    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $users = User::find($id);

        if (!$users) {
            return GlobalFunction::save(Message::NOT_FOUND);
        }

        $users->update([
            "id_prefix" => $request["personal_info"]["id_prefix"],
            "id_no" => $request["personal_info"]["id_no"],
            "first_name" => $request["personal_info"]["first"],
            "middle_name" => $request["personal_info"]["middle"],
            "last_name" => $request["personal_info"]["last"],
            "sex" => $request["personal_info"]["sex"],
            "role_id" => $request["role_id"],
            "location_name" => $request["location"],
            "department_name" => $request["department"],
            "company_name" => $request["company"],
        ]);
        return GlobalFunction::save(Message::USER_UPDATE, $users);
    }

    public function destroy($id)
    {
        $invalid_id = User::where("id", $id)
            ->withTrashed()
            ->get();

        if ($invalid_id->isEmpty()) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        $user = User::withTrashed()->find($id);
        $is_active = User::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $user->delete();
            $message = Message::ARCHIVE_STATUS;
        } else {
            $user->restore();
            $message = Message::RESTORE_STATUS;
        }

        return GlobalFunction::response_function($message, $user);
    }

    public function import_user(Request $request)
    {
        $import_user = $request->all();
        foreach ($import_user as $import) {
            $users = User::create([
                "account_code" => $import["personal_info"]["code"],
                "first_name" => $import["personal_info"]["first"],
                "middle_name" => $import["personal_info"]["middle"],
                "last_name" => $import["personal_info"]["last"],
                "sex" => $import["personal_info"]["sex"],
                "role_id" => 2,
                "location_name" => $import["location"]["name"],
                "department_name" => $import["department"]["name"],
                "company_name" => $import["company"]["name"],
            ]);
        }

        return GlobalFunction::save(Message::USER_SAVE, $import_user);
    }
}

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

class UserController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;
        $search = $request->search;
        $paginate = isset($request->paginate) ? $request->paginate : 1;

        $users = User::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })->when($search, function ($query) use ($search) {
            $query
                ->where("account_code", "like", "%" . $search . "%")
                ->orWhere("first_name", "like", "%" . $search . "%")
                ->orWhere("middle_name", "like", "%" . $search . "%")
                ->orWhere("last_name", "like", "%" . $search . "%")
                ->orWhere("sex", "like", "%" . $search . "%")
                ->orWhere("location_name", "like", "%" . $search . "%")
                ->orWhere("department_name", "like", "%" . $search . "%")
                ->orWhere("company_name", "like", "%" . $search . "%");
        });

        $users = $paginate
            ? $users->orderByDesc("updated_at")->paginate($request->rows)
            : $users->orderByDesc("updated_at")->get();

        $is_empty = $users->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        return GlobalFunction::response_function(Message::USER_DISPLAY, $users);
    }

    public function store(StoreRequest $request)
    {
        $users = User::create([
            "account_code" => $request["personal_info"]["code"],
            "first_name" => $request["personal_info"]["first"],
            "middle_name" => $request["personal_info"]["middle"],
            "last_name" => $request["personal_info"]["last"],
            "sex" => $request["personal_info"]["sex"],
            "role_id" => $request["role_id"],
            "location_name" => $request["location"]["name"],
            "department_name" => $request["department"]["name"],
            "company_name" => $request["company"]["name"],
        ]);
        return GlobalFunction::save(Message::USER_SAVE, $users);
    }
    public function login(Request $request)
    {
        $user = User::with("role")
            ->where("account_code", $request->account_code)
            ->first();

        if (!$user || ($request->account_code = !$user->account_code)) {
            throw ValidationException::withMessages([
                "account_code" => ["The provided credentials are incorrect."],
            ]);

            if ($user || $request->account_code == $user->account_code) {
                return GlobalFunction::response_function(
                    Message::INVALID_ACTION
                );
            }
        }
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
            "account_code" => $request["personal_info"]["code"],
            "first_name" => $request["personal_info"]["first"],
            "middle_name" => $request["personal_info"]["middle"],
            "last_name" => $request["personal_info"]["last"],
            "sex" => $request["personal_info"]["sex"],

            "location_name" => $request["location"]["name"],

            "department_name" => $request["department"]["name"],

            "company_name" => $request["company"]["name"],
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

        $user_id = Auth()->user()->id;
        $not_allowed = User::where("id", $id)
            ->where("id", $user_id)
            ->exists();

        if ($not_allowed) {
            return GlobalFunction::invalid(Message::INVALID_ACTION);
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
}

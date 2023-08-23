<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;

use App\Functions\GlobalFunction;
use App\Response\Message;

use App\Http\Requests\Role\DisplayRequest;
use App\Http\Requests\Role\StoreRequest;

class RoleController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;
        $search = $request->search;
        $paginate = isset($request->paginate) ? $request->paginate : 1;

        $role = Role::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })->when($search, function ($query) use ($search) {
            $query
                ->where("name", "like", "%" . $search . "%")
                ->orWhere("access_permission", "like", "%" . $search . "%");
        });

        $role = $paginate
            ? $role->orderByDesc("updated_at")->paginate($request->rows)
            : $role->orderByDesc("updated_at")->get();

        $is_empty = $role->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        return GlobalFunction::response_function(Message::ROLE_DISPLAY, $role);
    }
    public function store(StoreRequest $request)
    {
        $access_permission = $request->access_permission;
        $accessConvertedToString = implode(", ", $access_permission);
        $role = Role::create([
            "name" => $request->name,
            "access_permission" => $accessConvertedToString,
        ]);
        return GlobalFunction::save(Message::ROLE_SAVE, $role);
    }
    public function update(StoreRequest $request, $id)
    {
        $access_permission = $request->access_permission;
        $accessConvertedToString = implode(", ", $access_permission);

        $role = Role::find($id);
        $role->update([
            "name" => $request->name,
            "access_permission" => $accessConvertedToString,
        ]);
        return GlobalFunction::save(Message::ROLE_UPDATE, $role);
    }
    public function destroy($id)
    {
        $role = Role::where("id", $id)
            ->withTrashed()
            ->get();

        if ($role->isEmpty()) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $role = Role::withTrashed()->find($id);
        $is_active = Role::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $role->delete();
            $message = Message::ARCHIVE_STATUS;
        } else {
            $role->restore();
            $message = Message::RESTORE_STATUS;
        }
        return GlobalFunction::response_function($message, $role);
    }
}

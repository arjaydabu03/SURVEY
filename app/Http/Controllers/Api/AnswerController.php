<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Functions\GlobalFunction;
use App\Response\Message;

use App\Http\Requests\Questionaire\DisplayRequest;

use App\Models\Answer;

class AnswerController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;
        $search = $request->search;
        $paginate = isset($request->paginate) ? $request->paginate : 1;

        $answer = Answer::when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })->when($search, function ($query) use ($search) {
            $query->where("answer", "like", "%" . $search . "%");
        });

        $answer = $paginate
            ? $answer->orderByDesc("updated_at")->paginate($request->rows)
            : $answer->orderByDesc("updated_at")->get();

        $is_empty = $answer->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        return GlobalFunction::response_function(
            Message::ANSWER_DISPLAY,
            $answer
        );
    }
    public function store(Request $request)
    {
        $answer = Answer::create([
            "answer" => $request->answer,
        ]);
        return GlobalFunction::save(Message::ANSWER_SAVE, $answer);
    }
    public function update(Request $request, $id)
    {
        $answer = Answer::find($id);
        $answer->update([
            "answer" => $request->answer,
        ]);
        return GlobalFunction::save(Message::ANSWER_UPDATE, $answer);
    }
    public function destroy($id)
    {
        $answer = Answer::where("id", $id)
            ->withTrashed()
            ->get();

        if ($answer->isEmpty()) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $answer = Answer::withTrashed()->find($id);
        $is_active = Answer::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $answer->delete();
            $message = Message::ARCHIVE_STATUS;
        } else {
            $answer->restore();
            $message = Message::RESTORE_STATUS;
        }
        return GlobalFunction::response_function($message, $answer);
    }
}

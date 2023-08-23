<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Functions\GlobalFunction;
use App\Response\Message;

use App\Http\Requests\Questionaire\DisplayRequest;
use App\Http\Requests\Questionaire\StoreRequest;

use App\Models\Questionaire;
class QuestionController extends Controller
{
    //
    public function index(DisplayRequest $request)
    {
        $status = $request->status;
        $search = $request->search;
        $paginate = isset($request->paginate) ? $request->paginate : 1;

        $question = Questionaire::with("answers")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->when($search, function ($query) use ($search) {
                $query
                    ->where("question", "like", "%" . $search . "%")
                    ->orWhere("type", "like", "%" . $search . "%");
            });

        $question = $paginate
            ? $question->orderByDesc("updated_at")->paginate($request->rows)
            : $question->orderByDesc("updated_at")->get();

        $is_empty = $question->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        return GlobalFunction::response_function(
            Message::QUESTION_DISPLAY,
            $question
        );
    }
    public function store(StoreRequest $request)
    {
        $question = Questionaire::create([
            "question" => $request->question,
            "type" => $request->type,
        ]);
        $question->answers()->attach($request->answers);

        return GlobalFunction::save(Message::QUESTION_SAVE, $question);
    }
    public function update(StoreRequest $request, $id)
    {
        $question = Questionaire::find($id);
        $question->update([
            "question" => $request->question,
            "type" => $request->type,
        ]);
        $question->answers()->sync($request->answers);
        return GlobalFunction::save(Message::QUESTION_UPDATE, $question);
    }
    public function destroy($id)
    {
        $question = Questionaire::where("id", $id)
            ->withTrashed()
            ->get();

        if ($question->isEmpty()) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $question = Questionaire::withTrashed()->find($id);
        $is_active = Questionaire::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $question->delete();
            $message = Message::ARCHIVE_STATUS;
        } else {
            $question->restore();
            $message = Message::RESTORE_STATUS;
        }
        return GlobalFunction::response_function($message, $question);
    }
}

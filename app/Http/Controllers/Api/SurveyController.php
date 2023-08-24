<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Functions\GlobalFunction;
use App\Response\Message;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\Questionaire\DisplayRequest;

use App\Models\Survey;

class SurveyController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;
        $search = $request->search;
        $paginate = isset($request->paginate) ? $request->paginate : 1;

        $survey = Survey::with("question", "user")
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })
            ->when($search, function ($query) use ($search) {
                $query->where("answer", "like", "%" . $search . "%");
            });

        $survey = $paginate
            ? $survey->orderByDesc("updated_at")->paginate($request->rows)
            : $survey->orderByDesc("updated_at")->get();

        $is_empty = $survey->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        return GlobalFunction::response_function(
            Message::SURVEY_DISPLAY,
            $survey
        );
    }
    public function store(Request $request)
    {
        $user_id = Auth()->user()->id;

        foreach ($request->survey as $survey) {
            Survey::create([
                "user_id" => $user_id,
                "question_id" => $survey["id"],
                "answer" => $survey["answer"],
            ]);
        }

        return GlobalFunction::save(Message::SURVEY_SAVE, $request->survey);
    }
    public function update(Request $request, $id)
    {
        $survey = Survey::find($id);
        $survey->update([
            "answer" => $request->answer,
        ]);
        return GlobalFunction::save(Message::SURVEY_UPDATE, $survey);
    }
    public function destroy($id)
    {
        $survey = Survey::where("id", $id)
            ->withTrashed()
            ->get();

        if ($survey->isEmpty()) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $survey = Survey::withTrashed()->find($id);
        $is_active = Survey::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            $survey->delete();
            $message = Message::ARCHIVE_STATUS;
        } else {
            $survey->restore();
            $message = Message::RESTORE_STATUS;
        }
        return GlobalFunction::response_function($message, $survey);
    }
}

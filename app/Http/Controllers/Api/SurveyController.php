<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;

use App\Models\Survey;
use App\Response\Message;
use Illuminate\Http\Request;

use App\Functions\GlobalFunction;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SurveyResource;
use App\Http\Requests\Questionaire\DisplayRequest;

class SurveyController extends Controller
{
    public function index(DisplayRequest $request)
    {
        $status = $request->status;
        $search = $request->search;
        $paginate = isset($request->paginate) ? $request->paginate : 1;
        $month = $request->month;
        $year = $request->year;
        $department = $request->department;

        $survey = User::with("survey.question")
            ->where(function ($query) {
                return $query->whereHas("survey.question");
            })
            ->when($status === "inactive", function ($query) {
                $query->onlyTrashed();
            })

            ->when($department, function ($query) use ($department) {
                $query->where(
                    "department_name",
                    "like",
                    "%" . $department . "%"
                );
            })
            ->when($year && $month, function ($query) use ($year, $month) {
                $query->whereHas("survey", function ($query) use (
                    $year,
                    $month
                ) {
                    $query
                        ->whereMonth("created_at", $month)
                        ->whereYear("created_at", $year);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query
                    ->where("first_name", "like", "%" . $search . "%")
                    ->orWhere("middle_name", "like", "%" . $search . "%")
                    ->orWhere("last_name", "like", "%" . $search . "%");
            });

        $survey = $paginate
            ? $survey->orderByDesc("updated_at")->paginate($request->rows)
            : $survey->orderByDesc("updated_at")->get();

        $is_empty = $survey->isEmpty();

        if ($is_empty) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        new SurveyResource($survey);

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

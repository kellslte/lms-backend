<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sotu;
use App\Models\Task;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Mentor;
use App\Services\Chart;
use App\Models\Facilitator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {

        $response = [
            "facilitators" => Facilitator::count(),
            "mentors" => Mentor::count(),
            "sotu" => Sotu::count(),
            "tasks" => Task::count(),
            "lessons" => Lesson::all()->reject(fn ($lesson) => $lesson->status != "published")->count(),
            "lesson_performance" => Chart::render(Course::all()),
            "sotu_meetings" => [],
            "upcoming" => []
        ];


        return response()->json([
            "message" => "Dashboard content loaded successfully",
            "success" => true,
            "data" => [
                "response" => $response
            ]
        ], 200);
    }
}

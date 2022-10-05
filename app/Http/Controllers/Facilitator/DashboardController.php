<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Meeting;
use App\Services\Classroom as ClassroomAlias;
use Illuminate\Http\Request;
use App\Services\TaskManager;
use App\Services\TaskService;
use App\Services\LessonsService;
use App\Services\ScheduleService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = getAuthenticatedUser();

        $meetings = Meeting::where("host_name", $user->name)->count();

        $data =  [
            'published_lessons' => [],
            'unpublished_lessons' => [],
            'completed_tasks' => [],
            'pending_tasks' => [],
            'live_classes' => $meetings,
            'schedule' => ScheduleService::getSchedule($user),
            'course' => $user->course->title,
            'total_enrolled_students' => $user->course->students->count(),
        ];

        if($tasks = TaskManager::taskStatus($user->course->id)){
            $data["pending_tasks"] = $tasks["pending_tasks"];
            $data["completed_tasks"] = $tasks["graded_tasks"];
        }

        if($lessons = ClassroomAlias::allLessons($user)){
            $data["published_lessons"] = [...$lessons["published_lessons"]];
            $data["unpublished_lessons"] = [...$lessons["unpublished_lessons"]];
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}

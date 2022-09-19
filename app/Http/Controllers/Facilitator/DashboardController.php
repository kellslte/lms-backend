<?php

namespace App\Http\Controllers\Facilitator;

use App\Services\Classroom;
use Illuminate\Http\Request;
use App\Services\TaskManager;
use App\Services\TaskService;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = getAuthenticatedUser();

        $data =  [
            'published_lessons' => [],
            'unpublished_lessons' => [],
            'completed_tasks' => [],
            'pending_tasks' => [],
            'live_classes' => 21,
            'schedule' => json_decode($user->schedule->meetings, true),
            'course' => $user->course->title,
            'total_enrolled_students' => $user->course->students->count(),
        ];

        if($tasks = TaskManager::taskStatus($user->course->id)){
            if(!is_null($tasks)){
                $data["pending_tasks"] = $tasks["pending_tasks"];
            }
        }

        if($lessons = Classroom::allLessons($user)){
            if(!is_null($lessons)){
                $data["published_lessons"] = $lessons["published_lessons"];
                $data["unpublished_lessons"] = $lessons["unpublished_lessons"];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}

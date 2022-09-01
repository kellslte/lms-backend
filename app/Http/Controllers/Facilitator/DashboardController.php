<?php

namespace App\Http\Controllers\Facilitator;

use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = getAuthenticatedUser();
    
        $data =  [
            'published_lessons' => LessonsService::getPublishedLessons($user),
            'unpublished_lessons' => LessonsService::getUnpublishedLessons($user),
            'completed_tasks' => TaskService::getTasksCompletedByStudents($user),
            'pending_tasks' => TaskService::getTasksSubmittedButNotYetApproved($user),
            'live_classes' => 21,
            'schedule' => $user->schedule,
            'enrolled_students' => $user->course->students,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}

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

        $lessonService = new LessonsService($user);

        // TODO get data from database
        $data =  [
            'published_lessons' => $lessonService->getPublishedLessons(),
            'unpublished_lessons' => $lessonService->getUnpublishedLessons(),
            'completed_tasks' => TaskService::getTasksCompletedByStudents($user),
            'pending_tasks' => TaskService::getTasksSubmittedButNotYetApproved($user),
            'live_classes' => 21,
            'schedule' => $user->schedule
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}

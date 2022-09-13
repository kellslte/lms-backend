<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use App\Services\ScheduleService;
use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use App\Services\LessonsService;

class DashboardController extends Controller
{
    public function index(){
        $user = User::find(getAuthenticatedUser()->id);

        $user->load(['schedule', 'submissions']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'lessons' => LessonsService::getUserCurriculum($user),
                'leaderboard_position' => 2,
                'total_tasks_done' => count($user->completedTasks()),
                'schedule' => ScheduleService::getSchedule($user),
                'leaderboard' => LeaderboardService::getTrackBoard(getAuthenticatedUser())->take(5),
                'total_enrolled_students' => collect($user->course->students)->map(function($student){
                    return $student->email;
                }),
            ],
        ]);
    }
}

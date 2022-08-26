<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Services\ScheduleService;
use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use App\Services\LessonsService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $user = User::find(getAuthenticatedUser()->id);

        $user->load(['schedule', 'notifications', 'submissions']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'lessons' => LessonsService::getUserCurriculum($user),
                'leaderboard_position' => 2,
                'total_tasks_done' => 21,
                'schedule' => ScheduleService::getSchedule($user),
                'submissions' => $user->submissions,
                'leaderboard' => LeaderboardService::getTrackBoard(getAuthenticatedUser())->take(5),
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $user = User::find(Auth::id());

        $user->load(['schedule', 'notifications', 'submissions']);
        
        $notifications = $user->notifications;

        $schedule = [];

        $data = [
            'completed_lessons' => 21,
            'leaderboard_position' => 2,
            'total_tasks_done' => 21
        ];
    }
}

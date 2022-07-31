<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        
        $notifications = [];

        $schedule = [];

        $data = [
            'completed_lessons' => 21,
            'leaderboard_position' => 2,
            'total_tasks_done' => 21
        ];
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderboardController extends Controller
{
    public function __invoke(){
        $user = getAuthenticatedUser();

        $users = $user->course->students;

        // TODO get points and arrange the points in descending order;
        $board = collect($users)->map(function ($user) {
            return [
                "name" => $user->name,
                "attendances" => $user->point->attendance_points,
                "bonus" => $user->point->bonus_points,
                "task" => $user->point->task_points,
                "total" => $user->point->total,
            ];

        });

        return response()->json([
            'status' => 'Successfull',
            "data" => [
                "leaderboard" => $board
            ]
        ]);
    }
}

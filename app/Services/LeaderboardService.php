<?php 
namespace App\Services;

use App\Models\Submission;
use App\Models\User;

class LeaderboardService {
    public static function getTrackBoard($user){
        $users = $user->course->students;

        // TODO get points and arrange the points in descending order;
        $board = collect($users)->map(function ($user) {
            return [
                "name" => $user->name,
                "attendances" => $user->point->attendance_points,
                "bonus" => $user->point->bonus_points,
                "task" => $user->point->task_points,
                "total" => $user->point->total,
                "id" => $user->id,
                "email" => $user->email
            ];
        });

        return $board;
    }

    public static function getTotalLeaderBoard(){
        //$users = $user->course->students;
        // pull alll courses

        // puul students too

        // pull points too

        // render leaderboard

        // return data;
        $users = User::all();
    }
}
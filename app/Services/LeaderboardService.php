<?php 
namespace App\Services;

use App\Models\User;
use App\Models\Mentor;
use App\Models\Submission;

class LeaderboardService {
    public static function getTrackBoard($user){
        $users = $user->course->students;

        $mentors = Mentor::all();
        // get all mentees
        $mentors->load('mentees');

        // TODO get points and arrange the points in descending order;
        $board = collect($users)->map(function ($user) use ($mentors) {
            // check if student has a mentor
            $mentor = collect($mentors)->map(function($mentor) use ($user){
                $mentees = collect(json_decode($mentor->mentees->mentees, true));
                $record = $mentees->where("studentId", $user->id)->first();
                if($record){
                    return $mentor->id;
                }
            })->filter()->toArray();

            return [
                "name" => $user->name,
                "attendances" => $user->point->attendance_points,
                "bonus" => $user->point->bonus_points,
                "task" => $user->point->task_points,
                "total" => $user->point->total,
                "id" => $user->id,
                "email" => $user->email,
                "mentor" => $mentors->where("id", implode($mentor))->first()
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
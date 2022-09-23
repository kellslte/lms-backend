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

        return collect([...self::getBoard($users)]);
    }

    private static function getBoard($users){
         $mentors = Mentor::all();
        // get all mentees
        $mentors->load('mentees');

       return collect($users)->map(function ($user) use ($mentors) {
            // check if student has a mentor
            $mentor = collect($mentors)->map(function ($mentor) use ($user) {
                $mentees = collect(json_decode($mentor->mentees->mentees, true));
                $record = $mentees->where("studentId", $user->id)->first();
                if ($record) {
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
        })->sortBy(function($item){
            return $item["total"];
        });
    }

    public static function getUserPosition($user){
        $students = $user->course->students;

        $board = self::getBoard($students)->sortBy('total');

        return $board->search(function($item) use($user){
            return $item["id"] === $user->id;
        });
    }
}
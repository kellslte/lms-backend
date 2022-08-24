<?php 
namespace App\Services;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;

class ScheduleService {

    public static function getSchedule($user){
        return collect(json_decode($user->schedule->meetings))->map(function ($schedule) {
            $lesson = Lesson::find($schedule->lesson_id);

            return [
                "title" => $lesson->title,
                "tutor" => $lesson->course->facilitator->name,
                "date" => formatDate($lesson->updated_at),
                "time" => formatTime($lesson->updated_at),
            ];
        });
    }

    public static function addToSchedule(Model $user, Array $data){
        $meetings = json_decode($user->schedule->meetings);

        array_push($meetings, $data);

        $user->schedule->update([
            "meetings" => json_encode($meetings),
        ]);

        return $user;
    }
}
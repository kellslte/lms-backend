<?php 
namespace App\Services;

use App\Models\Lesson;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Model;

class ScheduleService {

    public static function getSchedule($user){
        return collect(json_decode($user->schedule->meetings))->map(function ($schedule) {
            return [
                "title" => $schedule->caption,
                "tutor" => $schedule->host_name,
                "date" => formatDate($schedule->date),
                "time" => formatTime($schedule->start_time),
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
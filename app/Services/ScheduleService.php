<?php 
namespace App\Services;

use App\Models\Sotu;
use App\Models\Lesson;
use App\Models\Meeting;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScheduleService {

    public static function getSchedule($user){
        $schedule = [
            "happening_today" => [],
            "happening_this_week" => [],
            "happening_this_month" => [],
            "sotu" => []
        ];

        $meetings = collect(json_decode($user->schedule->meetings, true));

        if($meetings->isEmpty()){
            return $schedule;
        }

        $week = $meetings->map(function ($val) {
            $meeting = Meeting::find($val["id"]);

            return [
                "caption" => $meeting->caption,
                "host" => $meeting->host_name,
                "date" => $val['date'],
                "start_time" => formatTime($meeting->start_time),
                "link" => $val['link'],
                "id" => $val['id'],
            ];
        })->groupBy(function ($val) {
            return Carbon::parse($val['date'])->format('W');
        });

        $month = $meetings->map(function ($val) {
            $meeting = Meeting::find($val["id"]);

            return [
                "caption" => $meeting->caption,
                "host" => $meeting->host_name,
                "date" => $val['date'],
                "start_time" => formatTime($meeting->start_time),
                "link" => $val['link'],
                "id" => $val['id'],
            ];
        })->groupBy(function ($val) {
            return Carbon::parse($val['date'])->format('M');
        });

        $day = $meetings->map(function ($val) {
            $meeting = Meeting::find($val["id"]);

            return [
                "caption" => $meeting->caption,
                "host" => $meeting->host_name,
                "date" => $val['date'],
                "start_time" => formatTime($meeting->start_time),
                "link" => $val['link'],
                "id" => $val['id'],
            ];
        })->groupBy(function ($val) {
            return Carbon::parse($val['date'])->format('D');
        });

        $schedule["happening_today"] = ($day->get(getDay(today()))) ? $day[getDay(today())]->map(function ($val) {
            $meeting = Meeting::find($val["id"]);

            return [
                "caption" => $meeting->caption,
                "host" => $meeting->host_name,
                "date" => $val['date'],
                "start_time" => formatTime($meeting->start_time),
                "link" => $val['link'],
                "id" => $val['id'],
            ];
        }) : [];

        $schedule["happening_this_week"] = $week[getWeek(today())] ?? [];

        $schedule["happening_this_month"] = $month[getMonth(today())] ?? [];

        $sotu = Sotu::all();

        $schedule["sotu"] = collect($sotu)->map(function ($meeting) {
                    return [
                        "link" => $meeting->link,
                        "done" => true,
                    ];
        });
        

        return $schedule;
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
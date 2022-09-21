<?php 
namespace App\Services;

use App\Models\Lesson;
use App\Models\Meeting;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScheduleService {

    public static function getSchedule($user){
        $schedule = [];

        $sotu = Meeting::whereCaption("State of The Union")->get();

        $week = collect(json_decode($user->schedule->meetings, true))->map(function ($val) {
            return [
                "caption" => $val['caption'],
                "host" => $val['host_name'],
                "date" => $val['date'],
                "start_time" => formatTime($val['start_time']),
                "end_time" => formatTime($val['end_time']),
                "link" => $val['link'],
                "id" => $val['id'],
            ];
        })->groupBy(function ($val) {
            return Carbon::parse($val['date'])->format('W');
        });

        $month = collect(json_decode($user->schedule->meetings, true))->map(function ($val) {
            return [
                "caption" => $val['caption'],
                "host" => $val['host_name'],
                "date" => $val['date'],
                "start_time" => formatTime($val['start_time']),
                "end_time" => formatTime($val['end_time']),
                "link" => $val['link'],
                "id" => $val['id'],
            ];
        })->groupBy(function ($val) {
            return Carbon::parse($val['date'])->format('M');
        });

        $day = collect(json_decode($user->schedule->meetings, true))->map(function ($val) {
            return [
                "caption" => $val['caption'],
                "host" => $val['host_name'],
                "date" => $val['date'],
                "start_time" => formatTime($val['start_time']),
                "end_time" => formatTime($val['end_time']),
                "link" => $val['link'],
                "id" => $val['id'],
            ];
        })->groupBy(function ($val) {
            return Carbon::parse($val['date'])->format('D');
        });

        $schedule["happening_today"] = ($day->get(getDay(today()))) ? $day[getDay(today())]->map(function ($val) {
            return [
                "caption" => $val['caption'],
                "host" => $val['host'],
                "date" => $val['date'],
                "start_time" => formatTime($val['start_time']),
                "end_time" => formatTime($val['end_time']),
                "link" => $val['link'],
                "id" => $val['id'],
            ];
        }) : [];

        $schedule["happening_this_week"] = $week[getWeek(today())] ?? [];

        $schedule["happening_this_month"] = $month[getMonth(today())] ?? [];

        $schedule["sotu"] = collect($sotu)->map(function ($meeting) {
            return ($meeting->date < today()) ? [
                "caption" => $meeting->caption,
                "host" => $meeting->host_name,
                "date" => $meeting->date,
                "start_time" => formatTime($meeting->start_time),
                "end_time" => formatTime($meeting->end_time),
                "link" => $meeting->link,
                "done" => true,
            ] : [
                "caption" => $meeting->caption,
                "host" => $meeting->host_name,
                "date" => $meeting->date,
                "start_time" => formatTime($meeting->start_time),
                "end_time" => formatTime($meeting->end_time),
                "link" => $meeting->link,
                "done" => false,
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
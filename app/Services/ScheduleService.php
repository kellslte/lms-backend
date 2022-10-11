<?php
namespace App\Services;

use App\Models\Sotu;
use App\Models\Lesson;
use App\Models\Meeting;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScheduleService {

    public static function getSchedule($user): array
    {
        $schedule = [
            "happening_today" => [],
            "happening_this_week" => [],
            "happening_this_month" => [],
            "sotu" => []
        ];

        if($user->schedule){
            $meetings = collect(json_decode($user->schedule->meetings, true));

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
            })->groupBy(fn($val) => Carbon::parse($val['date'])->format('W'));

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
            })->groupBy(fn($val) => Carbon::parse($val['date'])->format('M'));

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
            })->groupBy(fn($val) => Carbon::parse($val['date'])->format('D'));

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

            return self::extracted($week, $schedule, $month);
        }
        return self::extracted(collect([]), $schedule, collect([]));
    }

    public static function addToSchedule(Model $user, Array $data): Model
    {
        $meetings = json_decode($user->schedule->meetings);

        $meetings[] = $data;

        $user->schedule->update([
            "meetings" => json_encode($meetings),
        ]);

        return $user;
    }

    public static function displayAdminSchedule(Model $admin): array
    {
        $schedule = [
            "happening_today" => [],
            "happening_this_week" => [],
            "happening_this_month" => [],
            "sotu" => []
        ];

            $meetings =  Meeting::all();

            if($meetings->count() < 0){
                return self::extracted(collect([]), [], collect([]));
            }

            $week = $meetings->map(callback: fn($meeting) => [
                "caption" => $meeting->caption,
                "host" => $meeting->host_name,
                "date" => $meeting->date,
                "start_time" => formatTime($meeting->start_time),
                "link" => $meeting->link,
                "id" => $meeting->id,
            ])->groupBy(fn($val) => Carbon::parse($val->date)->format('W'));

            $month = $meetings->map(fn($meeting) => [
                "caption" => $meeting->caption,
                "host" => $meeting->host_name,
                "date" => $meeting->date,
                "start_time" => formatTime($meeting->start_time),
                "link" => $meeting->link,
                "id" => $meeting->id,
            ])->groupBy(fn($val) => Carbon::parse($val->date)->format('M'));

            $day = $meetings->map(fn($meeting) => [
                "caption" => $meeting->caption,
                "host" => $meeting->host_name,
                "date" => $meeting->date,
                "start_time" => formatTime($meeting->start_time),
                "link" => $meeting->link,
                "id" => $meeting->id,
            ])->groupBy(fn($val) => Carbon::parse($val->date)->format('D'));

            $schedule["happening_today"] = ($day->get(getDay(today()))) ? $day[getDay(today())]->map(function ($meeting) {
                return [
                    "caption" => $meeting->caption,
                    "host" => $meeting->host_name,
                    "date" => $meeting->date,
                    "start_time" => formatTime($meeting->start_time),
                    "link" => $meeting->link,
                    "id" => $meeting->id,
                ];
            }) : [];

        return self::extracted($week, $schedule, $month);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $week
     * @param array $schedule
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $month
     * @return array
     */
    public static function extracted(\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $week, array $schedule, \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $month): array
    {
        $schedule["happening_this_week"] = $week[getWeek(today())] ?? [];

        $schedule["happening_this_month"] = $month[getMonth(today())] ?? [];

        $sotu = Sotu::all();

        $schedule["sotu"] = $sotu->map(fn($meeting) => [
            "link" => $meeting->link,
            "done" => true,
        ]);

        return $schedule;
    }
}

<?php

namespace App\Http\Controllers\Student;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function __invoke()
    {
        $user = getAuthenticatedUser();

        $schedule = [];

        $week = collect(json_decode($user->schedule->meetings, true))->groupBy(function($val){
            return Carbon::parse($val['date'])->format('W');
        });

        $month = collect(json_decode($user->schedule->meetings, true))->groupBy(function($val){
            return Carbon::parse($val['date'])->format('M');
        });

        $day = collect(json_decode($user->schedule->meetings, true))->groupBy(function($val){
            return Carbon::parse($val['date'])->format('D');
        });

        $schedule["happening_today"] = $day[getDay(today())]->reject(function($val){
            return $val['date'] !== today();
        })->take(4);

        $schedule["happening_this_week"] = $week[getWeek(today())]->take(4);

        $schedule["happening_this_month"] = $month[getMonth(today())]->take(4);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                "schedule" => $schedule
            ]
        ]);
    }
}

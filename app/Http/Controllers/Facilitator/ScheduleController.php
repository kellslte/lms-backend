<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Meeting;
use App\Events\ClassFixed;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\AttendanceService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLiveClassRequest;

class ScheduleController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();


        $schedule = [];

        try {
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

            $schedule["happening_today"] = ($day->has(getDay(today()))) ? $day[getDay(today())]->reject(function ($val) {
                return $val['date'] !== today();
            })->map(function ($val) {
                return [
                    "caption" => $val['caption'],
                    "host" => $val['host_name'],
                    "date" => $val['date'],
                    "start_time" => formatTime($val['start_time']),
                    "end_time" => formatTime($val['end_time']),
                    "link" => $val['link'],
                    "id" => $val['id'],
                ];
            })->take(4) : [] ;

            $schedule["happening_this_week"] = $week[getWeek(today())]->take(4) ?? [];

            $schedule["happening_this_month"] = $month[getMonth(today())]->take(4) ?? [];

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
            } catch (\Exception $e) {
                $schedule["happening_today"] = [];
            $schedule["happening_this_week"] = [];
            $schedule["happening_this_month"] = [];
            $schedule["sotu"] = [];
            $schedule["error"] = $e->getMessage();
        }

        return response()->json([
            'status' => 'success',
            'schedule' => $schedule
        ], 200);
    }

    public function fixLiveClass(CreateLiveClassRequest $request){

        $user = getAuthenticatedUser();
        
        try{
            $class = Meeting::create([
                'host_name' => $user->name,
                'link' => $request->link,
                'start_time' => $request->time,
                'end_time' => $request->time,
                'date' => $request->date,
                'calendarId' => '6318e0104204e',
                'type' => "class",
                'caption' => $request->caption
            ]);

            // TODO fire class created event
            ClassFixed::dispatch($user->course->students, $class, $user);

            return response()->json([
                "status" => "successful",
                "message" => "Class has been fixed",
                "data" => [
                    "class" => $class
                ]
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ]);
        }
    }
}

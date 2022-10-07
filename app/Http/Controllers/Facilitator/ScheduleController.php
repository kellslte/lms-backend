<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Meeting;
use App\Events\ClassFixed;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\AttendanceService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLiveClassRequest;
use App\Services\ScheduleService;

class ScheduleController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = getAuthenticatedUser();

        try {
            $schedule = ScheduleService::getSchedule($user);

            return response()->json([
                "status" => "success",
                "data" => $schedule
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => "failed",
                "message" => $th->getMessage()
            ], 400);
        }
    }

    public function fixLiveClass(CreateLiveClassRequest $request): \Illuminate\Http\JsonResponse
    {

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

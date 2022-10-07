<?php

namespace App\Http\Controllers\Admin;

use App\Events\ClassFixed;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSotuRequest;
use App\Models\Facilitator;
use App\Models\Meeting;
use App\Models\Sotu;
use App\Services\ScheduleService;
use App\Http\Requests\CreateLiveClassRequest;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $schedule = ScheduleService::displayAdminSchedule(getAuthenticatedUser());

        return response()->json([
            'status' => 'success',
            'data' => [
                'schedule' => $schedule
            ]
        ], 200);
    }

    public function fixSotu(CreateSotuRequest $request){
        $user = getAuthenticatedUser();

        try{
            $sotu = Sotu::create([
                "link" => $request->link,
                "time" => $request->time,
            ]);

            return response()->json([
                "status" => "success",
                "data" => [
                    "sotu" => $sotu
                ]
            ], 201);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ], $e->getCode());
        }
    }

    public function updateSotu(){}

    public function fixClass(CreateLiveClassRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = getAuthenticatedUser();

        try {
            $meeting =  Meeting::create([
                "caption" => $request->caption,
                "host" => $request->host,
                "link" => $request->link,
                "start_time" => $request->time,
                "end_time"=> $request->time,
                "date" => $request->date,
                "calendarId" => '6318e0104204e',
                "type" => "class",
        ]);

        $host = Facilitator::whereName($request->host)->first();

         ClassFixed::dispatch($host->course->students, $meeting, $user);

         return response()->json([
                "status" => "successful",
                "message" => "Class has been fixed",
                "data" => [
                    "class" => $meeting
                ]
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ], 400);
        }
    }

    public function updateClass(CreateLiveClassRequest $request, Meeting $meeting): \Illuminate\Http\JsonResponse
    {
        $user = getAuthenticatedUser();

        try {
            $meeting->update([
                "caption" => $request->caption,
                "host" => $request->host,
                "link" => $request->link,
                "start_time" => $request->time,
                "end_time"=> $request->time,
                "date" => $request->date,
                "calendarId" => '6318e0104204e',
                "type" => "class",
        ]);

        $host = Facilitator::whereName($request->host)->first();

         ClassFixed::dispatch($host->course->students, $meeting, $user);

         return response()->json([
                "status" => "successful",
                "message" => "Class has been updated",
                "data" => [
                    "class" => $meeting
                ]
            ]);
        }catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ], 400);
        }
    }
}

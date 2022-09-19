<?php

namespace App\Http\Controllers\Facilitator;

use App\Events\ClassFixed;
use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLiveClassRequest;
use App\Services\AttendanceService;

class ScheduleController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();

        return response()->json([
            'status' => 'success',
            'schedule' => $user->schedule
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

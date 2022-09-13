<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLiveClassRequest;
use App\Services\AttendanceService;

class ScheduleController extends Controller
{
    protected $facilitator;
    protected $attendanceService;

    public function __construct(){
        $this->facilitator = getAuthenticatedUser();
        $this->attendanceService = new AttendanceService();
    }

    public function index(){
        $user = getAuthenticatedUser();

        return response()->json([
            'status' => 'success',
            'schedule' => $user->schedule
        ], 200);
    }

    public function fixLiveClass(CreateLiveClassRequest $request){
        // TODO create the live class
        $class = Meeting::create([
            'host_name' => $this->facilitator->name,
            'link' => $request->link,
            'time' => $request->time,
            'date' => $request->date,
            'type' => $request->type
        ]);

        // TODO make it attendable
        // $this->attendanceService->attend($class);
        // $this->attendanceService->setDate($request->date);

        // TODO attach it to those that should attend
        // TODO attach it to the schedule of the attendees
        foreach($this->facilitator->course->students as $student) {
            $this->attendanceService->attender($student);
            $student->schedule()->associate($class);
        }
        
        // TODO send notifications that class has been fixed
    }


    /*
    
    attendance array will now look like this: 

    $attendance = [
        "september" => [
            "wk1" => [],
            "wk2" => [],
            "wk3" => [],
            "wk5" => [],
        ],
        "october" => [
            "wk1" => [],
            "wk2" => [],
            "wk3" => [],
            "wk5" => [],
        ],
        "november" => [
            "wk1" => [],
            "wk2" => [],
            "wk3" => [],
            "wk5" => [],
        ],
        "december" => [
            "wk1" => [],
            "wk2" => [],
            "wk3" => [],
            "wk5" => [],
        ],
        "january" => [
            "wk1" => [],
            "wk2" => [],
            "wk3" => [],
            "wk5" => [],
        ],
        "february" => [
            "wk1" => [],
            "wk2" => [],
            "wk3" => [],
            "wk5" => [],
        ],
        "march" => [
            "wk1" => [],
            "wk2" => [],
            "wk3" => [],
            "wk5" => [],
        ],
    ];
    
    */
}

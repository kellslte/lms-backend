<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Services\LessonsService;
use App\Services\AttendanceService;
use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;

class ClassroomController extends Controller
{
    public function index(){
        return response()->json([
            'status' => 'success',
            'data' => [
                'lessons' => LessonsService::getClassroomData(getAuthenticatedUser()),
                'attendance' => AttendanceService::getRecord(getAuthenticatedUser())
            ]
        ], 200);
    }

    public function getLesson($lesson){

        $lesson = Lesson::where('id',$lesson)->first();

        if(!$lesson){
            return response()->json([
                "status" => "error",
                "message" => "Lesson not found"
            ], 404);
        }

        $resource = new LessonResource($lesson);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                "lesson" => $resource,
            ]
        ], 200);
    }

    public function markAttendance(Request $request, $meeting, $userId){
        // validate that the meeting exists
        $meeting = Meeting::whereId($meeting)->first();

        $user = User::whereId($userId)->first();

        // update student record with present at meeting
        $data = [
            "date" => today(),
            "meetingId" => $meeting->id,
        ];

        $record = AttendanceService::mark($user);

        // return response 
        return ($record) ? response()->json([
            "status" => "success",
            "message" => "attendance record has been marked successfully",
        ], 200) : response()->json([
            "status" => "failed",
            "message" => "attendance record could not be marked",
        ], 400);
    }

    public function getSotu(){
        return collect(json_decode(getAuthenticatedUser()->schedule->meetings, true));
    }

    public function incrementViewCount($lesson){
        if($studentLesson = Lesson::find($lesson)){
            $count = $studentLesson->views->count;

            $studentLesson->views->update([
                "count" => $count + 1,
            ]);

            return response()->json([
                "status" => "success",
                "messaged" => "View count updated successfully",
            ]);
        }
    }
}

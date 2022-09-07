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

    public function getLesson(Lesson $lesson){
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

        $record = AttendanceService::mark($data, $user);

        // return response 
        return ($record) ? response()->json([
            "status" => "success",
            "message" => "attendance record has been marked successfully",
        ], 200) : response()->json([
            "status" => "failed",
            "message" => "attendance record could not be marked",
        ], 400);
    }
}

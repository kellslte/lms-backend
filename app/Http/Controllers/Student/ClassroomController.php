<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\LessonsService;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(){
        return response()->json([
            'status' => 'success',
            'data' => [
                'lessons' => LessonsService::getClassroomData(getAuthenticatedUser()),
            ]
        ]);
    }

    public function getLesson(Lesson $lesson){
        return response()->json([
            'status' => 'success',
            'lesson' => $lesson
        ], 200);
    }

    public function getStudentLessons(){
        
    }
}

/* 

user lessons will now be called via get student lessons. As the the lesosns are being created the lesson ids are set in the student curriculum and it iwll look like this:

[
    ["lesson_id" => "id", "lesson_status" => "completed"],
]

*/

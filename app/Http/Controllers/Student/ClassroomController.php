<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
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
                'attendance' => json_decode(getAuthenticatedUser()->attendance->record, true)
            ]
        ]);
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
}

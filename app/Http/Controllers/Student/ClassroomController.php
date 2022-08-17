<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index($query = null){
        // TODO get lessons for that week and all its resources
        return response()->json([
            'status' => 'success',
            'data' => [
                'lessons' => [],
            ]
        ]);

        // TODO pull this from Youtube
    }

    public function getLesson(Lesson $lesson){}

    public function getStudentLessons(){}
}

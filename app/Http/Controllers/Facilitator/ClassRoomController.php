<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\Classroom;
use Illuminate\Http\Request;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLessonRequest;

class ClassRoomController extends Controller
{
    public function index(){
       $user = getAuthenticatedUser();

       $response = Classroom::allLessons($user);

       $code = (!is_null($response)) ? 200 : 400;

       $status = (!is_null($response)) ? 'success' : 'failed';
        
        return response()->json([
            'status' => $status,
            'data' => [
                'lessons' => $response,
            ]
        ], $code);
    }

    public function store(Request $request){
        $user = getAuthenticatedUser();

        return response()->json($user);

        return Classroom::createLesson($request, $user);
    }

    public function update(CreateLessonRequest $request, Lesson $lesson){
        $user = getAuthenticatedUser();
        
        return LessonsService::updateLesson($request, $user, $lesson);
    }

    public function delete(Lesson $lesson){
        $response = LessonsService::deleteLesson($lesson);

        if($response){
            return response()->json([
                'status' => 'success',
                'message' => 'Lesson deleted successfully.'
            ], 204);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Lesson could not be deleted successfully'
        ], 400);
    }
}

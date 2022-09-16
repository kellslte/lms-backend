<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLessonRequest;

class ClassRoomController extends Controller
{
    public function index(){
       $user = getAuthenticatedUser();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'lessons' => LessonsService::getAllLessons($user),
            ]
        ], 200);
    }

    public function store(Request $request){
        $user = getAuthenticatedUser();

        return response()->json($user);

        return LessonsService::createLesson($request, $user);
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

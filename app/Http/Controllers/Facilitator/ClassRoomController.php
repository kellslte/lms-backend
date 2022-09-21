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

       if(array_key_exists("error", $response)){
        return response()->json([
            "status" => "failed",
            "message" => $response["error"]  
        ], 400);
       }
        
        return response()->json([
            'status' => "successful",
            'data' => [
                'lessons' => $response,
            ]
        ]);
    }

    public function store(CreateLessonRequest $request){
        $user = getAuthenticatedUser();

        return Classroom::createLesson($request, $user->course);
    }

    public function saveAsDraft(CreateLessonRequest $request){
        $user = getAuthenticatedUser();

        return Classroom::saveLessonAsDraft($request, $user->course);
    }

    public function showLesson(String $lesson){
        $dblesson  = Lesson::find($lesson);

        if(!$lesson){
            return response()->json([
                "status" => "failed",
                "message" => "Lesson does not exist"    
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data" => [
                "lesson" => $dblesson->with("media", "resources")
            ],
        ]);
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

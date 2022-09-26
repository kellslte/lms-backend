<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\Classroom;
use Illuminate\Http\Request;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CreateLessonRequest;
use App\Http\Resources\FaciLessonResource;

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

    public function stageLesson(CreateLessonRequest $request){
        $user = getAuthenticatedUser();

        try {
            return $lesson = Classroom::stageLesson($request, $user->course);

            return response()->json([
                "status" => "success",
                "data" => [
                    'lesson' => $lesson
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage(),
            ]);
        }
    }

    public function store(CreateLessonRequest $request){
        $user = getAuthenticatedUser();

        try{
           return $lesson = Classroom::createLesson($request, $user->course);

            return response()->json([
                "status" => "success",
                "data" => [
                    'lesson' => $lesson
                ]
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage(),
            ]);
        }
    }

    public function saveAsDraft(CreateLessonRequest $request){
        $user = getAuthenticatedUser();

        $lesson = Classroom::saveLessonAsDraft($request, $user->course);

        if(is_null($lesson)){
            return response()->json([
               'status' => 'failed',
               'message' => 'Could not save the Lesson',
            ], 400);
        }

        return response()->json([
           'status' => "successful",
           'data' => [
               'lesson' => $lesson,
           ]], 200);
    }

    public function showLesson(String $lesson){
        $dblesson  = Lesson::find($lesson);

        if(!$lesson){
            return response()->json([
                "status" => "failed",
                "message" => "Lesson does not exist"    
            ], 404);
        }

        $response = [
            "id" => $dblesson->id,
            "title" => $dblesson->title,
            "description" => $dblesson->description,
            "thumbnail" => $dblesson->media->thumbnail,
            "videoLink" => $dblesson->media->video_link,
            "resources" => $dblesson->resources,
        ];

        return response()->json([
            "status" => "success",
            "data" => [
               "lesson" => $response
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

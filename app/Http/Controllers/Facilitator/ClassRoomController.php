<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;
use App\Services\Facilitator\Classroom;
use App\Http\Requests\CreateLessonRequest;

class ClassRoomController extends Controller
{
    public function index()
    {
       $user = getAuthenticatedUser();

       try {
            $response = Classroom::getLessons($user);

            return response()->json([
                "status" => "success",
                "data" => [
                    "lessons" => $response
                ]
            ], 200);
       } catch (\Throwable $th) {
            return response()->json([
                "status" => "failed",
                "data" => [],
                "message" => $th->getMessage()
            ], 422);
       }
    }

    public function store(CreateLessonRequest $request): \Illuminate\Http\JsonResponse
    {
        try{
            $response = Classroom::createLesson($request);

            return response()->json([
                "status" => "success",
                "data" => $response,
            ], 201);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "data" => [],
                "message" => $e->getMessage(),
            ], 400);
        }
    }

    public function showLesson(String $lesson): \Illuminate\Http\JsonResponse
    {
        
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

    public function update(CreateLessonRequest $request, Lesson $lesson): \Illuminate\Http\JsonResponse
    {
        $user = getAuthenticatedUser();

        return LessonsService::updateLesson($request, $user, $lesson);
    }

    public function delete(Lesson $lesson): \Illuminate\Http\JsonResponse
    {
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

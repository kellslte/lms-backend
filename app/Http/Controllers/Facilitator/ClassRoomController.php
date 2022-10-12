<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\Classroom;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLessonRequest;

class ClassRoomController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
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

    public function store(CreateLessonRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = getAuthenticatedUser();

        try{
            $course = Course::where("title", $request->track)->first() ?? $user->course;

            $response = Classroom::save($request, $course, $user->name);

            return response()->json([
                "status" => "success",
                "data" => [
                    'lesson' => $response["lesson"],
                    'thumbnail' => $response["lesson"]->media->thumbnail,
                    'video_link' => $response["lesson"]->media->video_link,
                    'resources' => $response["resources"],
                ]
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
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

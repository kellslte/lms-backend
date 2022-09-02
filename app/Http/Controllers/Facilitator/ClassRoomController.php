<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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

    public function store(CreateLessonRequest $request){
        $user = getAuthenticatedUser();

        $course = Course::firstWhere("title", $user->course->title);
        
            // create lesson
            $lesson = $course->lessons()->create([
                "title" => $request->title,
                "description" => $request->description,
                "tutor" => $user->name,
            ]);

            $request->merge([
                "tags" => $course->title
            ]);

            // upload video by calling youtube service
            $youtubeVideoDetails = getYoutubeVideoDetails($request);

            // upload transacript
            $tanscriptUrl = $request->file("lessonTranscript")->store("/" . $user->id, "public");
            
            // TODO: send notification that a new lesson has been uploaded


            // use returned video details to create video resource
            $lesson->media()->create([
                "video_link" => $youtubeVideoDetails["videoLink"],
                "thumbnail" => $youtubeVideoDetails["thumbnail"],
                "transcript" => Storage::url($tanscriptUrl),
                "youtube_video_id" => $youtubeVideoDetails["videoId"],
            ]);
            
            return response()->json([
                "status" => "success",
                "message" => "Your lesson has been created successfully.",
                "data" => [
                    "lesson" => $lesson
                ],
            ]);
            try{
        }catch(\Exception $e){
            return response()->json([
                "status" => "error",
                "message" => "Something happened and your lesson could not be created"
            ]);
        }
    }

    public function update(CreateLessonRequest $request, Lesson $lesson){
        $user = getAuthenticatedUser();
        
        $lesson->update([
            "title" => $request->title,
            "description" => $request->description,
            "tutor" => $user->name,
        ]);

        // $
    }

    public function delete(Lesson $lesson){}
}

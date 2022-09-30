<?php
namespace App\Services;

use App\Models\User;
use App\Models\Facilitator;
use App\Events\LessonCreated;
use App\Services\StudentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



class Classroom {
    public static function allLessons(Facilitator $user){

        $publishedLessons = [];
        $unpublishedLessons = [];

        try {
        $published = collect($user->course->lessons)->where("status", "published")->flatten();
        
                if($published){
                    $publishedLessons = $published->map(function ($lesson) use ($user) {
                        return [
                            "id" => $lesson->id,
                            "status" => $lesson->status,
                            "thumbnail" =>  isset($lesson->media->thumbnail) ? $lesson->media->thumbnail : null,
                            "title" => $lesson->title,
                            "description" => $lesson->description,
                            "datePublished" => formatDate($lesson->created_at),
                            "tutor" => $user->name,
                            "views" => $lesson->views->count,
                            "taskSubmissions" => TaskManager::getSubmissions($lesson->tasks, $user->course->students)->count()
                        ];
                    });
                }
    
                $unpublished = collect($user->course->lessons)->where("status", "unpublished")->flatten();

                if($unpublished){
                    $unpublishedLessons = $unpublished->map(function ($lesson) use ($user) {
                        return [
                            "id" => $lesson->id,
                            "status" => $lesson->status,
                            "thumbnail" => isset($lesson->media->thumbnail) ? $lesson->media->thumbnail : null,
                            "title" => $lesson->title,
                            "description" => $lesson->description,
                            "datePublished" => formatDate($lesson->created_at),
                            "tutor" => $user->name,
                            "views" => $lesson->views->count,
                            "taskSubmissions" => TaskManager::getSubmissions($lesson->tasks, $user->course->students)->count()
                        ];
                    });
                }
    
                return [
                    "published_lessons" => $publishedLessons,
                    "unpublished_lessons" => $unpublishedLessons,
                ];
        } catch (\Throwable $th) {
            return [
                "published_lessons" => [],
                "unpublished_lessons" => [],
                "error" => $th->getMessage()
            ];
        }        
    }

    public static function stageLesson($request, $course){
        // upload file to server
        $video = $request->file('lessonVideo')->store("/lessons", "public");
        $videoUrl = asset("/uploads/{$video}");

        $transcriptUrl = "";

        // upload transcript to server
        if($request->file("lessonTranscript")){
            $transcript = $request->file('lessonTranscript')->store("/transcripts", "public");
            $transcriptUrl = asset("/uploads/{$transcript}");
        }

        // upload lesson thumbnail to server
        $thumbnail = $request->file('lessonThumbnail')->store("/thumbnails", "public");
        $thumbnailUrl = asset("/uploads/{$thumbnail}");

        $lesson = $course->lessons()->create([
            "title" => $request->title,
            "description" => $request->description,
            "tutor" => $course->facilitator->name,
        ]);

        $lesson->media()->create([
            "video_link" => $videoUrl,
            "videoPath" => $video,
            "thumbnail" => $thumbnailUrl,
            "thumbnailPath" => $thumbnail,
            "transcript" => $transcriptUrl,
            "youtube_video_id" => ""
        ]);

        $lesson->views()->create();

        foreach($request->resources as $resource){
            $lesson->resources()->create([
                "title" => $resource["name"],
                "link" => $resource["link"],
                "type" => "file_link"
            ]);
        }

        try{
            if($course->title === "General Concepts & tooling"){
            $students = User::all();
            $students->load(['progress', 'curriculum']);
            // fire lesson created event
            LessonCreated::dispatch($students, $lesson);

            }else {
                $students = $course->students;
                $students->load(['progress', 'curriculum']);
                // fire lesson created event
                LessonCreated::dispatch($course->students, $lesson);

            }
        }catch(\Exception $e){
            return response()->json([
                "error" => $e->getMessage()
            ]);
        }

        return  $lesson;
    }

    public static function updateLesson($request, $lesson){
        try {
            if($lesson->status === "unpublished"){
                $videoPath = $lesson->media->video_link;
                $thumbnail = $lesson->media->thumbnail;
                $transcript = $lesson->transcript;

                $request->merge([
                    "tags" => $lesson->title,
                    "lessonVideo" => $videoPath,
                    "lessonThumbnail" => $thumbnail
                ]);

                $videoLink = getYoutubeVideoDetails($request);

                
            }

            $lesson->update([
                "title" => $request->title,
                "description" => $request->description,
                "status" => $request->status
            ]);

            $request->merge([
                "tags" => $lesson->course->title,
                "videoId" => $lesson->media->youtube_video_id,
            ]);

            if ($request->file('lessonVideo')) {
                $response = updateYoutubeVideoDetails($request);
            }

            if ($response) {
                return response()->json([
                    "status" => "success",
                    "message" => "Your video has been updated successfully",
                    "data" => [
                        "response" => $response
                    ]
                ], 200);
            }

            return response()->json([
                "status" => "success",
                "message" => "Your lesson has been updated successfully",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "error",
                'message' => "An error has occurred while updating the lesson.",
            ], 400);
        }   
    }

    private static function uploadTranscript($transcript){
        // upload transacript
        return $transcript->store("/transcripts", "public");
    }
}
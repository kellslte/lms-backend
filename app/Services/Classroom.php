<?php
namespace App\Services;

use App\Models\Facilitator;
use App\Events\LessonCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;



class Classroom {
    public static function allLessons(Facilitator $user){

        $publishedLessons = [];
        $unpublishedLessons = [];

        try {
                $published = collect($user->course->lessons)->where("status", "published");

                if($published){
                    $publishedLessons = $published->map(function ($lesson) use ($user) {
                        return [
                            "id" => $lesson->id,
                            "status" => $lesson->status,
                            "thumbnail" => $lesson->media->thumbnail,
                            "title" => $lesson->title,
                            "description" => $lesson->description,
                            "datePublished" => formatDate($lesson->created_at),
                            "tutor" => $user->name,
                            "views" => 0,
                            "taskSubmissions" => TaskManager::getSubmissions($lesson->task, $user->course->students)->count()
                        ];
                    });
                }
    
                $unpublished = collect($user->course->lessons)->where("status", "unpublished");

                if($unpublished){
                    $unpublishedLessons = $unpublished->map(function ($lesson) use ($user) {
                        return [
                            "id" => $lesson->id,
                            "status" => $lesson->status,
                            "thumbnail" => $lesson->media->thumbnail,
                            "title" => $lesson->title,
                            "description" => $lesson->description,
                            "datePublished" => formatDate($lesson->created_at),
                            "tutor" => $user->name,
                            "views" => 0,
                            "taskSubmissions" => TaskManager::getSubmissions($lesson->task, $user->course->students)->count()
                        ];
                    });
                }
    
                return [
                    "published_lessons" => $publishedLessons,
                    "unpublished_lessons" => $unpublishedLessons
                ];
        } catch (\Throwable $th) {
            return [
                "published_lessons" => [],
                "unpublished_lessons" => [],
                "error" => $th->getMessage()
            ];
        }        
    }

    public static function createLesson($request, $course){
        try{
            // try to upload vide to youtube
            $response = getYoutubeVideoDetails($request);

            // upload transcript and return the path
            $transcript = self::uploadTranscript($request->file('lessonTranscript'));

            // create lesson
            $lesson = $course->lessons()->create([
                "title" => $request->title,
                "description" => $request->description,
                "tutor" => $course->facilitator->name,
            ]);

            // create lesson media
            $lesson->media()->create([
                "video_link" => $response["videoLink"],
                "thumbnail" => $response["thumbnail"],
                "transcript" => $transcript,
                "youtube_video_id" => $response["youtube_video_id"]
            ]);

            foreach ($request->resources as $resource) {
                $lesson->resources()->create([
                    "type" => "file_link",
                    "title" => $resource["title"],
                    "resource" => $resource["link"]
                ]);
            }

            // update students lesson progress detail
            // TODO fire lesson creation event
            event(new LessonCreated($course->students));

            return response()->json([
                "status" => "successful",
                "message" => "Your lesson has been successfully created",
                "data" => [
                    "lesson" => $lesson
                ]
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ]);
        }
        
    }

    public static function saveLessonAsDraft($request, $course){
        
        try{
            // upload lesson cideo to our server 
            $videoPath = Storage::putFile("lessons", $request->file("lessonVideo"));

            // upload lesson thumnail
            $path = Storage::putFile("thumbnails", $request->file("lessonThumbnail"));
    
            // upload lesson transcript
            $transcript = self::uploadTranscript($request->file("lessonTranscript"));
    
            DB::transaction(function() use ($videoPath, $transcript, $request, $path, $course){
                // create lesson
                $lesson = $course->lessons()->create([
                    "title" => $request->title,
                    "description" => $request->description,
                    "tutor" => $course->facilitator->name,
                    "status" => "unpublished"
                ]);

                // create lesson media
                $lesson->media()->create([
                    "video_link" => asset("uploads/".$videoPath),
                    "thumbnail" => asset("uploads/" . $path),
                    "transcript" => asset("uploads/" . $transcript),
                    "youtube_video_id" => ""
                ]);

                // foreach ($request->resources as $resource) {
                //     $lesson->resources()->create([
                //         "type" => "file_link",
                //         "title" => $resource["title"],
                //         "resource" => $resource["link"]
                //     ]);
                // }

                return response()->json([
                    "status" => "successful",
                    "message" => "Your lesson draft has been successfully created",
                    "data" => [
                        "lesson" => $lesson
                    ]
                ]);
            });
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ]);
        }

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
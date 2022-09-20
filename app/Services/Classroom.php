<?php
namespace App\Services;

use App\Models\Facilitator;
use App\Events\LessonCreated;
use Illuminate\Support\Facades\Storage;



class Classroom {
    public static function allLessons(Facilitator $user){
        try {
            $lessons = $user->course->lessons;
            if ($lessons){
                $published = collect($user->course->lessons)->reject(function($lesson){
                    return $lesson->status !== "published";
                })->map(function($lesson) use ($user){
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
    
                $unpublished = collect($user->course->lessons)->reject(function($lesson){
                    return $lesson->status !== "unpublished";
                })->map(function($lesson)use ($user){
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
    
                return [
                    "published_lessons" => $published,
                    "unpublished_lessons" => $unpublished
                ];
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
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
            $videoPath = Storage::putFileAs("lessons", $request->file("lessonVideo"), $request->title);
    
            // upload lesson thumnail
            $path = Storage::putFileAs("thumbnails", $request->file("lessonThumnail"), $request->title);
    
            // upload lesson transcript
            $transcript = self::uploadTranscript($request->file("lessonTranscript"));
    
            // create lesson
            $lesson = $course->lessons()->create([
                "title" => $request->title,
                "description" => $request->description,
                "tutor" => $course->facilitator->name,
                "status" => "unpublished"
            ]);
    
            // create lesson media
            $lesson->media()->create([
                "video_link" => $videoPath,
                "thumbnail" => $path,
                "transcript" => $transcript,
                "youtube_video_id" => ""
            ]);
    
            foreach ($request->resources as $resource) {
                $lesson->resources()->create([
                    "type" => "file_link",
                    "title" => $resource["title"],
                    "resource" => $resource["link"]
                ]);
            }
    
            return response()->json([
                "status" => "successful",
                "message" => "Your lesson draft has been successfully created",
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
        $file = $transcript->store("/transcripts", "public");

        return Storage::url($file);
    }
}
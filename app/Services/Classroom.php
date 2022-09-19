<?php
namespace App\Services;

use App\Models\Facilitator;
use Illuminate\Support\Facades\Storage;



class Classroom {
    public static function allLessons(Facilitator $user){
        try {
            $published = collect($user->course->lessons)->reject(function($lesson){
                return $lesson->status !== "published";
            })->map(function($lesson) use ($user){
                return [
                    "id" => $lesson->id,
                    "status" => $lesson->status,
                    "thumbnail" => $lesson->thumbnail,
                    "title" => $lesson->title,
                    "description" => $lesson->description,
                    "datePublished" => formatDate($lesson->created_at),
                    "tutor" => $user->name,
                    "views" => "",
                    "taskSubmissions" => TaskManager::getSubmissions($lesson->task, $user->course->students)
                ];
            });

            $unpublished = collect($user->course->lessons)->reject(function($lesson){
                return $lesson->status !== "unpublished";
            })->map(function($lesson)use ($user){
                return [
                    "id" => $lesson->id,
                    "status" => $lesson->status,
                    "thumbnail" => $lesson->thumbnail,
                    "title" => $lesson->title,
                    "description" => $lesson->description,
                    "datePublished" => formatDate($lesson->created_at),
                    "tutor" => $user->name,
                    "views" => 0,
                    "taskSubmissions" => TaskManager::getSubmissions($lesson->task, $user->course->students)
                ];
            });

            return [
                "published_lessons" => $published,
                "unpublished_lessons" => $unpublished
            ];
        } catch (\Throwable $th) {
            return null;
        }        
    }

    public static function createLesson($request, $course){
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

        // update students lesson progress details

        // send notification to students that lesson has been created
        
    }

    public static function saveLessonAsDraft(){}

    public static function updateLesson(){}

    private static function uploadTranscript($transcript){
        // upload transacript
        $file = $transcript->store("/transcripts", "public");

        return Storage::url($file);
    }
}
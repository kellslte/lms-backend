<?php
namespace App\Services;

use App\Models\Facilitator;



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
            })->map(function($lesson){
                return 
            });

            return [
                "published_lessons" => $published,
                "unpublished_lessons" => $unpublished
            ];
        } catch (\Throwable $th) {
            return null;
        }        
    }

    public static function createLesson(){
        // 
    }

    public static function saveLessonAsDraft(){}

    public static function updateLesson(){}
}
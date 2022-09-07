<?php
namespace App\Services;

use App\Models\Lesson;

class ProgressService {
    public static function upcomingLessons(){
        $user = getAuthenticatedUser();

        $lessons = collect($user->lessons());

        return collect(json_decode($user->progress->course_progress, true))->map(function($lesson) use ($lessons){            
            $returnedlesson = $lessons->where("id", $lesson["lesson_id"])->first();

            return($lesson["percentage"] >= 0)? [
                "lesson" => $returnedlesson,
                "media" => $returnedlesson->media,
                "resource" => $returnedlesson->resources,
            ]: null;

        })->filter();
    }

    public static function updateLessonProgress($lessonId){
        $user = getAuthenticatedUser();

        $progress = collect(json_decode($user->progress->course_progress, true));

        $lesson = $progress->where("lesson_id", $lessonId)->first();

        $progress->pull($lesson);

        
    }
}
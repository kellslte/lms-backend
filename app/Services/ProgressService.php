<?php
namespace App\Services;

use App\Models\Lesson;

class ProgressService {
    public static function upcomingLessons(){
        $user = getAuthenticatedUser();

        $lessons = $user->lessons();

        return collect(json_decode($user->progress->course_progress, true))->map(function($lesson){            
            $returnedLesson = Lesson::find($lesson->id);

            return ($lesson["percentage"] >= 0) ? $returnedLesson->with('resources', 'media') : null;

        })->filter()->take(2);
    }

    public static function updateLessonProgress($lessonId){
        $user = getAuthenticatedUser();

        $progress = collect(json_decode($user->progress->course_progress, true));

        $lesson = $progress->where("lesson_id", $lessonId)->first();

        $progress->pull($lesson);

        
    }
}
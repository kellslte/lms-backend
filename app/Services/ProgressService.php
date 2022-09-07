<?php
namespace App\Services;

use App\Models\Lesson;

class ProgressService {
    public static function upcomingLessons(){
        $user = getAuthenticatedUser();

        $lessons = collect($user->lessons());

        return collect(json_decode($user->progress->course_progress, true));

        return collect(json_decode($user->progress->course_progress, true))->map(function($lesson) use ($lessons){            
            $returnedlesson = $lessons->where("id", $lesson["lesson_id"])->first();
            
            $returnedlesson->load(['media', 'resources']);

            return [
                "lesson" => $returnedlesson,
                "percentage" => $lesson["percentage"]
            ];
        });
    }

    public static function incrementStudentProgress($user, $request, $lessonId){
        $request->validate([
            "percentage" => "required|numeric",
        ]);

        $progress = collect(json_decode($user->progress->course_progress, true));

        $lesson = $progress->where("lesson_id", $lessonId)->first();

        $lesson["percentage"] = $request->percentage;

        $new = $progress->reject(function($item) use ($lesson){
            return $item["lesson_id"] === $lesson["lesson_id"];
        });
        
        $newArray = [...$new, $lesson];
        
        return $user->progress->update([
            "course_progress" => json_encode($newArray),
        ]);
    }
}
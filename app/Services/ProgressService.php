<?php
namespace App\Services;

use App\Models\Lesson;

class ProgressService {
    public static function upcomingLessons(){
        $user = getAuthenticatedUser();

        $lessons = collect($user->lessons());

        return collect(json_decode($user->progress->course_progress, true))->map(function($lesson) use ($lessons){            
            $returnedlesson = $lessons->where("id", $lesson["lesson_id"])->first();
            
            if($returnedlesson){
                $returnedlesson->load(['media', 'resources']);
                return [
                    "lesson" => $returnedlesson,
                    "percentage" => $lesson["percentage"]
                ];
            }
        });
    }

    public static function incrementStudentProgress($user, $request, $lessonId){
        $request->validate([
            "percentage" => "required|numeric",
        ]);

        $progress = collect(json_decode($user->progress->course_progress, true))->map(function($lesson) use ($lessonId, $request){
            if($lesson["id"] === $lessonId){
                $lesson["percentage"] = $request->percentage;
            }

            return $lesson;
        });
        
        return $user->progress->update([
            "course_progress" => json_encode($progress),
        ]);
    }
}
<?php

namespace App\Observers;

use UpdateProgress;
use UpdateCurriculum;
use App\Models\Lesson;

class LessonObserver
{
    public $afterCommit = true;

    /**
     * Handle the Lesson "updated" event.
     * 
     * @param  \App\Models\Lesson $lesson
     * @return void
    */
    public function create(Lesson $lesson){
        $students = $lesson->course->students;

        foreach($students as $student){
            // UpdateCurriculum::execute($student, $lesson);
            // UpdateProgress::execute($student, $lesson);
        }
    }



    /**
     * Handle the Lesson "retrieved" event.
     *
     * @param  \App\Models\Lesson  $lesson
     * @return void
     */
    public function retrieved(Lesson $lesson)
    {
        $students = $lesson->course->students;

        // foreach ($students as $student) {
        //     // pull student progress information
        //     $student->load('progress');

        //     if(!$student->progress){
        //         $student->progress()->create([
        //             "course" => $lesson->course->title,
        //             "course_progress" => json_encode([])
        //         ]);
        //     }
            
        //     $progress = collect(json_decode($student->progress->course_progress, true));

        //     $lessonProgress = $progress->where("lesson_id", $lesson->id)->first();

        //     if($lessonProgress){
        //         if($lessonProgress["percentage"] === 100){
        //             $count = $lesson->views->count + 1;
        //             $lesson->views->update([
        //                 "count" => $count,
        //             ]); 
        //         }
        //     }
        // }
    }
}
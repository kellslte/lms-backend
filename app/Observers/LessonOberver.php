<?php

namespace App\Observers;

use App\Models\Lesson;

class LessonOberver
{
    /**
     * Handle the Lesson "retrieved" event.
     *
     * @param  \App\Models\Lesson  $lesson
     * @return void
     */
    public function retrieved(Lesson $lesson)
    {
        $students = $lesson->course->students;

        foreach ($students as $student) {
            // pull student progress information
            $progress = collect(json_decode($student->progress->course_progress, true));

            $lessonProgress = $progress->where("lesson_id", $lesson->id)->first();

            if($lessonProgress["percentage"] === 100){
                $count = $lesson->views->count + 1;
                $lesson->views->update([
                    "count" => $count,
                ]); 
            }
        }
    }


}

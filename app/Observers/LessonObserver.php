<?php

namespace App\Observers;

use App\Actions\Notifier;
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
    public function created(Lesson $lesson): void
    {
        $count = 0;

        foreach ($lesson->course->students as $student) {
            // update progress
            $progress = $student->progress;

            $courseProgress = json_decode($progress->course_progress, true);

            $progress->update([
                "course_progress" => json_encode([...$courseProgress, [
                    "lesson_id" => $lesson->id,
                    "percentage" => 0
                ]])
            ]);

            // update curriculum
            $curriculum = $student->curriculum;

            $courseCurriculum = json_decode($curriculum->viewables, true);

            $curriculum->update([
                "viewables" => json_encode([...$courseCurriculum, [
                "lesson_id" => $lesson->id,
                "lesson_status" => "uncompleted"
                ]])
            ]);

            $count++;
        }


        Notifier::dm("personal", "A new lesson has been uploaded successfully to the {$lesson->course->title} track and {$count} students' progress and curriculum has been updated");

        Notifier::notify($lesson->course->title, "A new lesson has been uploaded. Go to your dashboard to check it out!");
    }

    /**
     * Handle the Lesson "retrieved" event.
     *
     * @param  \App\Models\Lesson  $lesson
     * @return void
     */
    public function retrieved(Lesson $lesson)
    {

    }
}

<?php

namespace App\Observers;

use App\Actions\Notifier;
use App\Models\Lesson;
use App\Models\User;

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

        $students = User::where("course_id", $lesson->course->id)->get();

        $students->load(["curriculum", "progress"]);

        foreach($students as $student) {
            // update progress
            $student->load("progress");

            $courseProgress = json_decode($student->progress->course_progress, true);

            $student->progress->update([
                "course_progress" => json_encode([...$courseProgress, [
                    "lesson_id" => $lesson->id,
                    "percentage" => 0
                ]])
            ]);

            // update curriculum
            $student->load("curriculum");

            $courseCurriculum = json_decode($student->curriculum->viewables, true);

            $student->curriculum->update([
                "viewables" => json_encode([...$courseCurriculum, [
                "lesson_id" => $lesson->id,
                "lesson_status" => "uncompleted"
                ]])
            ]);

            $count++;
        }

        Notifier::dm("personal", "A new lesson has been uploaded successfully to the {$lesson->course->title} track and {$count} students' progress and curriculum has been updated");

        Notifier::notify($lesson->course->title, "A new lesson has been uploaded to the {$lesson->course->title} track. Please go to your dashboard to check it out!");
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

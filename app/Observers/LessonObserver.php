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
        $students = $lesson->course->students;
    }
}

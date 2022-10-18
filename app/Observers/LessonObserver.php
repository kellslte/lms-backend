<?php

namespace App\Observers;

use Spatie\SlackAlerts\Facades\SlackAlert;
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
    public function created(Lesson $lesson): void
    {
        SlackAlert::message("A new lesson has been uploaded in the {$lesson->course->title} track!");
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

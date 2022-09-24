<?php

namespace App\Listeners;

use App\Events\LessonCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\NotifyStudentWhenLessonCreated;

class NotifyStudentsOfLessonCreation
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LessonCreated  $event
     * @return void
     */
    public function handle(LessonCreated $event)
    {
        foreach($event->students as $student){
            $student->notify(new NotifyStudentWhenLessonCreated());
        }
    }
}

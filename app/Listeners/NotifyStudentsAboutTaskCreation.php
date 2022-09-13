<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Notifications\NotifyStudentsOnTaskCreation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyStudentsAboutTaskGrade
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
     * @param  \App\Events\TaskCreated  $event
     * @return void
     */
    public function handle(TaskCreated $event)
    {
        $students = $event->task->lesson->course->students;

        Notification::send($students, new NotifyStudentsOnTaskCreation());
    }
}

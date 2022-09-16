<?php

namespace App\Listeners;

use App\Events\TaskGraded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotifyStudentWhenTaskGraded;

class NotifyStudentAboutTaskGrade
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
     * @param  \App\Events\TaskGraded  $event
     * @return void
     */
    public function handle(TaskGraded $event)
    {
        Notification::send($event->submissions->taskable, new NotifyStudentWhenTaskGraded());
    }
}

<?php

namespace App\Listeners;

use App\Events\TaskGraded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        //
    }
}

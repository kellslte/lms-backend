<?php

namespace App\Listeners;

use App\Events\LessonCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        //
    }
}

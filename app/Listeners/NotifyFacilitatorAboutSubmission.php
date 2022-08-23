<?php

namespace App\Listeners;

use App\Events\TaskSubmitted;
use App\Notifications\NotifyFacilitatorOnTaskSubmission;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyFacilitatorAboutSubmission
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
     * @param  \App\Events\TaskSubmitted  $event
     * @return void
     */
    public function handle(TaskSubmitted $event)
    {
        $facilitator = $event->submission->submittable->lesson->course->facilitator;

        Notification::send($facilitator, new NotifyFacilitatorOnTaskSubmission($event->submission));
    }
}

<?php

namespace App\Observers;

use App\Models\Course;
use App\Models\Meeting;
use App\Actions\Notifier;
use App\Models\Facilitator;

class MeetingObserver
{
    public $afterCommit = true;

    /**
     * Handle the Meeting "created" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function created(Meeting $meeting)
    {
        $tutor = Facilitator::where("name", $meeting->host_name)->first();

        $courseTitle = $tutor->course->title;

        Notifier::dm("personal", "{$tutor} just created a new meeting");

        Notifier::notify($courseTitle, "A live meeting has been scheduled, go to your dashboard and check it out!");
    }

    /**
     * Handle the Meeting "updated" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function updated(Meeting $meeting)
    {
        //
    }

    /**
     * Handle the Meeting "deleted" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function deleted(Meeting $meeting)
    {
        //
    }

    /**
     * Handle the Meeting "restored" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function restored(Meeting $meeting)
    {
        //
    }

    /**
     * Handle the Meeting "force deleted" event.
     *
     * @param  \App\Models\Meeting  $meeting
     * @return void
     */
    public function forceDeleted(Meeting $meeting)
    {
        //
    }
}

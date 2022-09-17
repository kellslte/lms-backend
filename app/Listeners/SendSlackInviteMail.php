<?php

namespace App\Listeners;

use App\Mail\SlackInviteMail;
use App\Events\SendSlackInvite;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSlackInviteMail
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
     * @param  \App\Events\SendSlackInvite  $event
     * @return void
     */
    public function handle(SendSlackInvite $event)
    {
        foreach ($event->students as $student) {
            Mail::to($student->email)->send(new SlackInviteMail($student, $event->link));
        }
    }
}

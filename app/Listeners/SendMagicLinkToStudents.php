<?php

namespace App\Listeners;

use App\Events\SendMagicLink;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotifyAdminOnMagicLinkSending;

class SendMagicLinkToStudents
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
     * @param  \App\Events\SendMagicLink  $event
     * @return void
     */
    public function handle(SendMagicLink $event)
    {
        foreach($event->users as $user){
            $user->sendMagicLink();
        }

        Notification::send($event->admins, new NotifyAdminOnMagicLinkSending);
    }
}

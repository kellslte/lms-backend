<?php

namespace App\Listeners;

use App\Events\SendMagicLink;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
    }
}

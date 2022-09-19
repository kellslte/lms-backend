<?php

namespace App\Listeners;

use App\Events\ClassFixed;
use App\Notifications\LiveClassFixed as NotificationsLiveClassFixed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LiveClassFixed
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
     * @param  \App\Events\ClassFixed  $event
     * @return void
     */
    public function handle(ClassFixed $event)
    {
        foreach($event->students as $student){
            $student->notify(new NotificationsLiveClassFixed());
        }
    }
}

<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use App\Events\LeaderboardUpdated as LeaderboardUpdatedEvent;
use App\Notifications\LeaderboardUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class UpdateStudentsOnLeaderboardUpdate
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
     * @param  \App\Events\LeaderboardUpdated  $event
     * @return void
     */
    public function handle(LeaderboardUpdatedEvent $event)
    {
        Notification::send($event->users, new LeaderboardUpdated());
    }
}

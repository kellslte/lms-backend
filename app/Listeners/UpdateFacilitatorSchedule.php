<?php

namespace App\Listeners;

use App\Events\ClassFixed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateFacilitatorSchedule
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
        // check if the facilitator has a schedule or create one if he doesn't
        $event->facilitator->schedule()->firstOrCreate([
            "meetings" => json_encode([])
        ]);

        $schedule = json_decode($event->facilitator->schedule->meetings, true);

        $schedule[] = $event->meeting;

        $event->facilitator->schedule->update([
            "meetings" => json_encode($schedule)
        ]);
    }
}

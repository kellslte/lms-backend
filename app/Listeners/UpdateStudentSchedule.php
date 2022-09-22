<?php

namespace App\Listeners;

use App\Events\ClassFixed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStudentSchedule
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
            $schedule = json_decode($student->schedule->meetings, true);

            $schedule[] = [
             "id" => $event->meeting->id,
             "caption" => $event->meeting->caption,
             "date" => formatDate($event->meeting->date),
             "link" => $event->meeting->link,
             "host_name" => $event->meeting->host_name,
             "type" => $event->meeting->type,
             "time" => formatTime($event->meeting->time)   
            ];

            $student->schedule->update([
                "meetings" => json_encode($schedule)
            ]);
        }
    }
}

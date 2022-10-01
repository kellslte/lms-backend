<?php

namespace App\Listeners;

use App\Events\LessonCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LessonProgressUpdate
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        info("Lesson progress updated for all students");
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LessonCreated  $event
     * @return void
     */
    public function handle(LessonCreated $event)
    {
        foreach($event->students as $student){
            $progress = json_decode($student->progress->course_progress, true);

            $progressRecord[] = [
                "lesson_id" => $event->lesson->id,
                "percentage" => 0
            ];

            $student->progress->update([
                "course_progress" => json_encode([...$progress, $progressRecord])
            ]);

            info("The lesson progress record has been updated for {$student->name}");
        }
    }
}

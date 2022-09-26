<?php

namespace App\Listeners;

use App\Events\LessonCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStudentCurriculum
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
     * @param  \App\Events\LessonCreated  $event
     * @return void
     */
    public function handle(LessonCreated $event)
    {
        foreach($event->students as $student){
            if(!$student->curriculum){
                $student->curriculum()->create([
                    "vieawables" => json_encode([])
                ]);
            }

            $curriculum = collect(json_decode($student->curriculum->viewables, true));

            $curriculum->merge([
                "lesson_id" => $event->lesson->id,
                "lesson_status" => "uncompleted"
            ]);

            $student->curriculum->update([
                "viewables" => json_encode($curriculum),
            ]);
        }
    }
}

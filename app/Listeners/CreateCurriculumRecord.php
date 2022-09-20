<?php

namespace App\Listeners;

use App\Events\CreateCurriculum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateCurriculumRecord
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
     * @param  \App\Events\CreateCurriculum  $event
     * @return void
     */
    public function handle(CreateCurriculum $event)
    {
        foreach ($event->students as $student) {
            if(!$student->curriculum){
                return $student->curriculum()->create([
                    "viewables" => json_encode([])
                ]);
            }
        }
    }
}

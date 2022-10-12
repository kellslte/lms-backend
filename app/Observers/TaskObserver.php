<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{


    /**
     * Handle the Task "updated" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function updated(Task $task)
    {
        //
    }

    /**
     * Handle the Task "retrieved" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function retrieved(Task $task)
    {
        if(!compareDates($task->task_deadline_date)){
            $task->update([
                "status" => "expired"
            ]);
        }
    }

    /**
     * Handle the Task "force deleted" event.
     *
     * @param  \App\Models\Task  $task
     * @return void
     */
    public function forceDeleted(Task $task)
    {
        //
    }
}

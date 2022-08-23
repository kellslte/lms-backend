<?php

namespace App\Observers;

use App\Models\Point;

class PointObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\Point  $user
     * @return void
     */
    // public function retrieved(Point $point)
    // {
    //     $total = ($point->attendance_points + $point->bonus_points + $point->task_points);
        
    //     $point->total = $total;

    //     return $point->save();
    // }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\Point  $user
     * @return void
     */
    public function updated(Point $point)
    {
        $total = ($point->attendance_points + $point->bonus_points + $point->task_points);
        
        $point->total = $total;

        return $point->save();
    }
}

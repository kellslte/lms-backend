<?php

namespace App\Observers;

use App\Models\Timeline;
use Carbon\Carbon;

class TimelineObserver
{
    public $afterCommit = true;

    /**
     * Handle the Timeline "force deleted" event.
     *
     * @param  \App\Models\Timeline  $timeline
     * @return void
     */
    public function retrieved(Timeline $timeline)
    {
        $date1 = Carbon::createFromDate($timeline->end_date);
        $date2 = Carbon::createFromDate(today());

        info("Timeline retrieved");
        info($date1->lte($date2)." {$date1} is greater than {$date2}");
        info("Timeline retrieved");

        if($date1->lte($date2)){
            $timeline->update([ "done" =>  true]);
        }
    }
}

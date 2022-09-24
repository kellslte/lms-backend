<?php

namespace App\Observers;

use App\Models\Timeline;

class TimelineOberver
{
    /**
     * Handle the Timeline "force deleted" event.
     *
     * @param  \App\Models\Timeline  $timeline
     * @return void
     */
    public function retrieved(Timeline $timeline)
    {
        if($timeline->end_date < today()){
            $timeline->update([ "done" =>  true]);
        }
    }
}

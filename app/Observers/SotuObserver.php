<?php

namespace App\Observers;

use App\Actions\Notifier;
use App\Models\Sotu;
use App\Models\User;
use App\Notifications\SotuCreatedNotification;
use App\Notifications\SotuUpdatedNotification;
use Illuminate\Support\Facades\Notification;

class SotuObserver
{
    public bool $afterCommit = true;

    /**
     * Handle the Sotu "created" event.
     *
     * @param Sotu $sotu
     * @return void
     */
    public function created(Sotu $sotu): void
    {
        Notifier::notify("", "SOTU has been fixed! Go to your dashboard to join thr meeting");
    }

    /**
     * Handle the Sotu "updated" event.
     *
     * @param Sotu $sotu
     * @return void
     */
    public function updated(Sotu $sotu)
    {
        Notification::send(User::all(), new SotuUpdatedNotification());
    }

    /**
     * Handle the Sotu "deleted" event.
     *
     * @param Sotu $sotu
     * @return void
     */
    public function deleted(Sotu $sotu)
    {
        //
    }

    /**
     * Handle the Sotu "restored" event.
     *
     * @param Sotu $sotu
     * @return void
     */
    public function restored(Sotu $sotu)
    {
        //
    }

    /**
     * Handle the Sotu "force deleted" event.
     *
     * @param Sotu $sotu
     * @return void
     */
    public function forceDeleted(Sotu $sotu)
    {
        //
    }
}

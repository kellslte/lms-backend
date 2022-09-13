<?php

namespace App\Providers;

use App\Events\LeaderboardUpdated;
use App\Events\LessonCreated;
use App\Events\TaskSubmitted;
use App\Listeners\NotifyFacilitatorAboutSubmission;
use App\Listeners\NotifyStudentsAboutTaskGrade;
use App\Listeners\NotifyStudentsOfLessonCreation;
use App\Listeners\UpdateStudentsOnLeaderboardUpdate;
use App\Models\Point;
use App\Notifications\NotifyStudentsOnTaskCreation;
use App\Observers\PointObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LeaderboardUpdated::class => [
            UpdateStudentsOnLeaderboardUpdate::class,
        ],
        TaskCreated::class => [
            NotifyStudentsOnTaskCreation::class,
        ],
        TaskGraded::class => [
            NotifyStudentsAboutTaskGrade::class,
        ],
        TaskSubmitted::class => [
            NotifyFacilitatorAboutSubmission::class,
        ],
        LessonCreated::class => [
            NotifyStudentsOfLessonCreation::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Point::observe(PointObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}

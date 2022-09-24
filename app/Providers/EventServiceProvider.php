<?php

namespace App\Providers;

use App\Models\Point;
use App\Models\Lesson;
use App\Models\Timeline;
use App\Events\ClassFixed;
use App\Events\LessonCreated;
use App\Events\SendMagicLink;
use App\Events\TaskSubmitted;
use App\Events\SendSlackInvite;
use App\Events\CreateCurriculum;
use App\Observers\PointObserver;
use App\Listeners\LiveClassFixed;
use App\Observers\LessonObserver;
use App\Events\LeaderboardUpdated;
use App\Observers\TimelineObserver;
use App\Listeners\SendSlackInviteMail;
use Illuminate\Auth\Events\Registered;
use App\Listeners\LessonProgressUpdate;
use App\Listeners\UpdateStudentSchedule;
use App\Listeners\CreateCurriculumRecord;
use App\Listeners\SendMagicLinkToStudents;
use App\Listeners\UpdateFacilitatorSchedule;
use App\Listeners\NotifyStudentsAboutTaskGrade;
use App\Listeners\NotifyStudentsOfLessonCreation;
use App\Listeners\NotifyFacilitatorAboutSubmission;
use App\Notifications\NotifyStudentsOnTaskCreation;
use App\Listeners\UpdateStudentsOnLeaderboardUpdate;
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
            LessonProgressUpdate::class,
        ],
        SendMagicLink::class => [
            SendMagicLinkToStudents::class,
        ],
        SendSlackInvite::class => [
            SendSlackInviteMail::class,
        ],
        ClassFixed::class => [
            LiveClassFixed::class,
            UpdateFacilitatorSchedule::class,
            UpdateStudentSchedule::class,
        ],
        CreateCurriculum::class => [
            CreateCurriculumRecord::class
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
        Lesson::observe(LessonObserver::class);
        Timeline::observe(TimelineObserver::class);
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

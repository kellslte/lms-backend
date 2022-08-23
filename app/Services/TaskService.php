<?php
namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Submission;
use App\Models\Facilitator;

class TaskService {

    // These methods will be called by the student

    public static function totalTasksCompleted(User $user){
        $tasks = Task::whereStudentId($user->id)->get();

        return collect($tasks)->map(function(Task $task){
            return $task->submitted === true;
        });
    }

    public static function totalTasks(User $user){
        return Task::whereStudentId($user->id)->count();
    }

    public static function expiredTasks($user){
        $tasks = Task::whereStudentId($user->id)->get();

        return collect($tasks)->map(function(Task $task){
          return  $task->status === 'expired';
        })->count();
    } 

    // These methods will be called by the facilitator

    // Get tasks related to the lessons the facilitator is handling
    public static function getTasksCompletedByStudents(Facilitator  $user){
        // get tasks related to the lessons that have been submitted
        $submittedTasks = self::getSubmittedTasks($user);

        return (collect($submittedTasks)->every(function ($value, $key) {
            return $value->count() > 0;
        })) ? collect($submittedTasks)->map(fn ($task) => ($task->status === 'approved') ? $task->submittable : []) : [] ;
    }

    public static function getTasksSubmittedButNotYetApproved(Facilitator $user){
        // get tasks related to the lessons that have been submitted
        $submittedTasks = self::getSubmittedTasks($user);

        return ((collect($submittedTasks)->every(function ($value, $key) {
            return $value->count() > 0;
        }))) ? collect($submittedTasks)->map(fn($task) => ($task->status === 'submitted') ? $task->submittable : []) : [];
    }

    public static function getRunningTasks(Facilitator $user){
        $lessons = $user->course->lessons;

        // load lesson tasks
        $lessons->load('task');

        return collect($lessons)->map(fn ($lesson) => ($lesson->task->status === 'running') ? $lesson->task : []);
    }

    public static function getUnpublishedTasks(Facilitator $user){
        $lessons = $user->course->lessons;

        // load lesson tasks
        $lessons->load('task');

        return collect($lessons)->map(fn ($lesson) => ($lesson->task->status === 'unpublished') ? $lesson->task : []);
    }

    protected static function getSubmittedTasks($user){
        $lessons = $user->course->lessons;

        // load lesson tasks
        $lessons->load('task');

        // get tasks related to the lessons that have been submitted
        return collect($lessons)->map(function ($lesson) {
            return $lesson->task->submissions;
        });
    }
}
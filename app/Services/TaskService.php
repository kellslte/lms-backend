<?php
namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Submission;
use App\Models\Facilitator;

class TaskService {

    // These methods will be called by the student
    public static function totalTasksCompleted(User $user){
        $tasks = $user->submissions;

        return collect($tasks)->map(function(Task $task){
            return $task->status === 'submitted';
        });
    }

    public static function totalTasks(User $user){
        $lessons = $user->course->lessons;

        return collect($lessons)->map(function($lesson){
            return $lesson->task;
        })->count();
    }

    public static function expiredTasks($user){
        $lessons = $user->course->lessons;

        return collect($lessons)->reject(function($lesson){
          return  $lesson->task->status !== 'expired';
        })->count();
    }
}
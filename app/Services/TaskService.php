<?php
namespace App\Services;

use App\Models\Task;

class TaskService {

    public static function totalTasksCompleted($user){
        $tasks = Task::whereStudentId($user->id)->get();

        return collect($tasks)->map(function(Task $task){
            return $task->submitted === true;
        })->count();
    }

    public static function totalTasks($user){
        return Task::whereStudentId($user->id)->count();
    }

    public static function expiredTasks($user){
        $tasks = Task::whereStudentId($user->id)->get();

        return collect($tasks)->map(function(Task $task){
          return  $task->status === 'expired';
        })->count();
    } 
}
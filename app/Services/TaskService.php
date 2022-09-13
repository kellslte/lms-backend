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

    // These methods will be called by the facilitator

    // Get tasks related to the lessons the facilitator is handling
    public static function getTasksCompletedByStudents(){
        // get tasks related to the lessons that have been submitted
        return is_null(self::getAllSubmissionsForTasks()) ? []: self::getAllSubmissionsForTasks();
    }

    public static function getTasksSubmittedButNotYetApproved(Facilitator $user){
        // get tasks related to the lessons that have been submitted
        return is_null(self::getAllSubmissionsForTasks()) ? []: self::getAllSubmissionsForTasks();
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

    protected static function getAllPendingTasksForStudents(){
        $submissions = self::getAllSubmissionsForTasks();

        $facilitator = getAuthenticatedUser();

        $lessons = $facilitator->course->lessons;

        $tasks = collect($lessons)->map(function ($lesson) {
            return $lesson->task;
        });

        $students = $facilitator->course->students;
        
        
    }

    protected static function getAllSubmissionsForTasks(){
        $facilitator = getAuthenticatedUser();

        $lessons = $facilitator->course->lessons;

       $tasks = collect($lessons)->map(function($lesson){
        return $lesson->task;
       });

       $students = $facilitator->course->students;

       // Get all submissions for tasks    
       return collect($tasks)->map(function($task) use ($students){
            foreach($students as $student){
                $submission = collect(json_decode($student->submissions->tasks, true))->where("id", $task->id)->first();

                return ($submission) ? [
                    "student_id" => $student->id,
                    "task_id" => $submission["id"],
                    "title" => $submission["title"],
                    "description" => $submission["description"],
                    "linkToResource" => $submission["linkToResource"],
                    "grade" => $submission["grade"],
                    "submission_date" => $submission["date_submitted"],
                ] : null;
            }
       })->filter()->groupBy("student_id");
    }

    public static function getSubmissions($task){
        $users = User::all();

        return collect($users)->map(function($user) use ($task) {
            return collect(json_decode($user->submissions->tasks, true))->reject(function($item) use ($task){
                return $item["id"] !== $task->id;
            });
        });
    }
}
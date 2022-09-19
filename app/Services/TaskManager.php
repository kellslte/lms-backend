<?php
namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Events\TaskGraded;
use App\Events\TaskCreated;


class TaskManager{

    public static function getSubmissions(Object $task, $students)
    {
        return collect($students)->map(function ($user) use ($task) {
            return collect(json_decode($user->submissions->tasks, true))->reject(function ($item) use ($task) {
                return $item["id"] !== $task->id;
            });
        });
    }

    public static function taskStatus(String $course){

        $lessons = Course::find($course)->lessons;

        $lessons ?? $lessons->load('task');

        try{
            $pending = collect($lessons)->reject(function($lesson){
                return $lesson->task->status !== "pending";
            })->map(function($lesson){
                return [
                    "id" => $lesson->task->id,
                    "title" => $lesson->task->title,
                    "description" => $lesson->task->description,
                    "task_deadline_date" => formatDate($lesson->task_deadline_date),
                    "task_deadline_time" => formatTime($lesson->task_deadline_time),
                    "lesson_id" => $lesson->id,
                    "status" => $lesson->task->status,
                    "submissions" => self::totalSubmissions($lesson->task, $lesson->course->students)
                ];
            })->toArray();

            $published = collect($lessons)->reject(function($lesson){
                return $lesson->task->status !== "published";
            })->map(function ($lesson) {
                return [
                    "id" => $lesson->task->id,
                    "title" => $lesson->task->title,
                    "description" => $lesson->task->description,
                    "task_deadline_date" => formatDate($lesson->task_deadline_date),
                    "task_deadline_time" => formatTime($lesson->task_deadline_time),
                    "lesson_id" => $lesson->id,
                    "status" => $lesson->task->status,
                    "submissions" => self::totalSubmissions($lesson->task, $lesson->course->students)
                ];
            })->toArray();

            $graded = collect($lessons)->reject(function($lesson){
                return $lesson->task->status !== "graded";
            })->map(function ($lesson) {
                return [
                    "id" => $lesson->task->id,
                    "title" => $lesson->task->title,
                    "description" => $lesson->task->description,
                    "task_deadline_date" => formatDate($lesson->task_deadline_date),
                    "task_deadline_time" => formatTime($lesson->task_deadline_time),
                    "lesson_id" => $lesson->id,
                    "status" => $lesson->task->status,
                    "submissions" => self::totalSubmissions($lesson->task, $lesson->course->students)
                ];
            })->toArray();

             return [
                    "pending_tasks" => [...$pending],
                    "published_tasks" => [...$published],
                    "graded_tasks" => [...$graded]
                ];
        }
        catch(\Exception $e){
            return null;
        }
    }

    public static function gradeTask(Task $task, User $student, Int $grade){
        // get the data for the student
        $submissions = $student->submissions;

        // find the task in the submissions collection for the student and grade it
        $newRecord = collect(json_decode($submissions->tasks, true))->map(function($item) use ($task, $grade){
            if($item["id"] === $task->id){
                $item["grade"] = $grade;
                $item["date_graded"] = today();
            }

            return $item;
        });

        if(!$submissions->update([
            "tasks" => json_encode($newRecord)
        ])){
            return null;
        }

        // TODO send a notification to user once the task has been graded
        TaskGraded::dispatch($submissions);

        return true;
    }

    public static function createTask(Array $task, Lesson $lesson, $users){
        try{
            $lesson->task()->create([
                "title" => $task["title"],
                "description" => $task["description"],
                "status" => $task["status"],
                "task_deadline_date" => $task["deadline_date"],
                "task_deadline_time" => $task["deadline_time"]
            ]);

            // TODO send a notification to user once task has been created
            TaskCreated::dispatch($users);

            return [
                "status" => true,
                "task" => $task
            ];
        }
        catch(\Exception $e){
            return [
                "status" => false,
                "task" => $e->getMessage()
            ];
        }
    }

    public static function updateTask(Array $data, Task $task){
        try{
            $task->update([
                "title" => $data["title"],
                "description" => $data["description"],
                "task_deadline_date" => $data["deadline_date"],
                "status" => $data["status"],
                "task_deadline_time" => $data["deadline_time"]
            ]);

            return [
                "status" =>  true,
                "task" => $task
            ];
        }
        catch(\Exception $e){
            return [
                "status" =>  false,
                "task" =>  $e->getMessage()
            ];
        }
    }

    public static function closeTasksubmission(Task $task){
        try{
            $task->update([
                "status" => "expired"
            ]);

            return [
                "status" =>  true,
                "task" => $task
            ];
        }
        catch(\Exception $e){
            return [
                "status" =>  false,
                "task" =>  $e->getMessage()
            ];
        }
    }

    public static function totalSubmissions(Task $task, $students){
        // get the submissions for each task
        return collect($students)->map(function($student) use ($task){
            $entry =
            collect(json_decode($student->submissions->tasks, true))->where("id", $task->id)->first();

            return ($entry) ? [
                "student_id" => $student->id,
                "student_name" => $student->name,
                "submission" => $entry
            ]: null; 
        })->filter();
    }
}
<?php
namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Events\TaskGraded;
use App\Events\TaskCreated;
use Illuminate\Http\Request;


class TaskManager{

    public static function getSubmissions($tasks, $students)
    {
        return collect($students)->map(function ($user) use ($tasks) {
            $submissions = collect(json_decode($user->submissions->tasks, true));

            if($submissions->count() > 0){
                return collect($tasks)->map(fn($task) => $submissions->firstWhere("id", $task->id));
            }

            return [];

        })->filter();
    }

    public static function taskStatus(String $course){

        $lessons = Course::find($course)->lessons;

        $lessons->load('tasks');

        $tasks = collect($lessons)->map(fn($lesson) => $lesson->tasks)->filter()->flatten();

        $pendingTasks = [];
        $gradedTasks = [];

        try{
            if($tasks){
                $pendingTasks = $tasks->where("status", "pending")->map(fn($task) => [
                    "id" => $task->id,
                    "title" => $task->title,
                    "description" => $task->description,
                    "task_deadline_date" => formatDate($task->task_deadline_date),
                    "task_deadline_time" => formatTime($task->task_deadline_time),
                    "lesson_id" => $task->lesson->id,
                    "status" => $task->status,
                    "submissions" => self::totalSubmissions($task, $task->lesson->course->students)
                ])->toArray();

                $gradedTasks = $tasks->where("status", "graded")->map(fn($task) => [
                    "id" => $task->id,
                    "title" => $task->title,
                    "description" => $task->description,
                    "task_deadline_date" => formatDate($task->task_deadline_date),
                    "task_deadline_time" => formatTime($task->task_deadline_time),
                    "lesson_id" => $task->lesson->id,
                    "status" => $task->status,
                    "submissions" => self::totalSubmissions($task, $task->lesson->course->students)
                ])->toArray();

                return [
                       "pending_tasks" => [...$pendingTasks],
                       "graded_tasks" => [...$gradedTasks],
                   ];
            }
        }
        catch(\Exception $e){
            return [
                "pending_tasks" => [],
                "published_tasks" => [],
                "error" => $e->getMessage()
            ];
        }
    }

    public static function gradeTask(Task $task, User $student, Int $grade): ?bool
    {
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

        TaskGraded::dispatch($submissions);

        return true;
    }

    public static function createTask(Array $task, Lesson $lesson, $users): array
    {
        try{
            $lesson->tasks()->create([
                "title" => $task["title"],
                "description" => $task["description"],
                "status" => $task["status"],
                "task_deadline_date" => $task["deadline_date"],
                "task_deadline_time" => $task["deadline_time"]
            ]);

            // TODO send a notification to user once task has been created
            TaskCreated::dispatch($users);

            return [
                "status" => "success",
                "task" => $task
            ];
        }
        catch(\Exception $e){
            return [
                "status" => "failed",
                "task" => $e->getMessage()
            ];
        }
    }

    public static function updateTask(Array $data, Task $task): array
    {
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

    public static function closeTasksubmission(Task $task): array
    {
        try{
            $task->update([
                "status" => "expired"
            ]);

            return [
                "status" =>  "success",
                "task" => $task,
                "message" => "Task submission closed",
                "code" => 200,
            ];
        }
        catch(\Exception $e){
            return [
                "status" =>  "failed",
                "message" =>  $e->getMessage(),
                "task" => [],
                "code" => 400
            ];
        }
    }

    public static function markAsGraded(Task $task): array
    {
        try{
            $task->update([
                "status" => "graded"
            ]);

            return [
                "status" =>  "success",
                "task" => $task,
                "message" => null,
                "code" => 200
            ];
        }catch(\Exception $e){
            return [
                "status" =>  "failed",
                "message" =>  $e->getMessage(),
                "task" => [],
                "code" => 400,
            ];
        }
    }

    public static function totalSubmissions(Task $task, $students): array
    {
        // get the submissions for each task
        return collect($students)->map(function($student) use ($task){
            $entry = collect(json_decode($student->submissions->tasks, true))->firstWhere("id", $task->id);

            return ($entry) ? [
                "student_id" => $student->id,
                "student_name" => $student->name,
                "submission" => $entry
            ]: null;
        })->filter()->all();
    }

    public static function editTaskGrade(Task $task, User $student, Request $request): \Illuminate\Http\JsonResponse
    {
        $submissions = collect(json_decode($student->submissions->tasks, true));

        try {
            $newCollection = $submissions->map(function($submission) use ($task, $request){
                if($submission["id"] === $task->id){
                    $submission["grade"] = $request->grade;
                    $submission["date_graded"] = today();
                }

                return $submission;
            });

            $student->submissions->update([
                "tasks" => json_encode($newCollection)
            ]);

            return response()->json([
                "status" => "successful",
                "message" => "student task has been edited"
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ], $e->getCode());
        }
    }
}

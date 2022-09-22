<?php

namespace App\Http\Controllers\Student;

use App\Models\Task;
use App\Models\Submission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskSubmissionRequest;

class TaskController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();

        $tasks = collect($user->course->lessons)->map(function($lesson) use ($user){
            $tasks = collect($user->completedTasks());

            return collect($lesson->tasks)->map(function($task) use ($tasks){
                
                $taskRE = $tasks->where("id", $task->id)->first();
                
                if($taskRE){
                    return [
                        "id" => $taskRE->id,
                        "title" => $taskRE->title,
                        "status" => ($taskRE) ? "submitted" : $taskRE->status,
                        "description" => $taskRE->description,
                        "task_deadline_date" => formatDate($taskRE->task_deadline_date),
                        "task_deadline_time" => formatTime($taskRE->task_deadline_time),
                        "lesson_id" => $taskRE->lesson->id,
                    ];
                }

                return [
                    "id" => $task->id,
                    "title" => $task->title,
                    "status" => ($task) ? "submitted" : $task->status,
                    "description" => $task->description,
                    "task_deadline_date" => formatDate($task->task_deadline_date),
                    "task_deadline_time" => formatTime($task->task_deadline_time),
                    "lesson_id" => $task->lesson->id,
                ];
            })->flatten();
            
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'completed_tasks' => $user->completedTasks(),
                'pending_tasks' => $user->pendingTasks(),
                'expired_tasks' => $user->expiredTasks(),
                'tasks' => $tasks,
            ]
        ]);
    }

    public function submit(Task $task, TaskSubmissionRequest $request){
        $user = getAuthenticatedUser();

        if($task->expired()){
            return response()->json([
             'status' => 'failed',
             'message' => 'You cannot submit this task anymore',
            ], 400);
        }

        $submissions = $user->submissions;

        $submittedTasks = collect(json_decode($submissions->tasks, true));

        if($submittedTasks->where("id", $task->id)->first()){
            return response()->json([
                'status' => 'failed',
                "messaged" => 'You can only submit a task once',
            ], 400);
        }

        $submittedTasks[] = [
            "id" => $task->id,
            "linkToResource" => $request->linkToResource,
            "student_name" => $user->name,
            "status" => "submitted",
            "title" => $task->title,
            "description" => $task->description,
            "date_submitted" => today(),
            "grade" => 0,
            "date_graded" => null,
        ];

        $user->submissions->update([
            "tasks" => $submittedTasks
        ]);

        // TODO add notification for task submission

        return response()->json([
            'status' => 'success',
            'message' => 'Task submitted successfully',
        ], 201);
    }
}

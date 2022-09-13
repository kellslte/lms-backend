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

        $tasks = collect($user->course->lessons)->map(function($lesson){
            return [
                "id" => $lesson->task->id,
                "title" => $lesson->task->title,
                "status" => $lesson->task->status,
                "description" => $lesson->task->description,
                "task_deadline_date" => formatDate($lesson->task->task_deadline_date),
                "task_deadline_time" => formatTime($lesson->task->task_deadline_time),
                "lesson_id" => $lesson->id,
            ];
        })->reject(function($task){
            return $task["status"] === "expired";
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

        if($submittedTasks->contains($task->id)){
            return response()->json([
                'status' => 'failed',
                "messaged" => 'You can only submit a task once',
            ], 400);
        }

        $submittedTasks[] = [
            "id" => $task->id,
            "linkToResource" => $request->linkToResource,
            "status" => "submitted",
            "title" => $task->title,
            "description" => $task->description,
            "date_submitted" => today(),
            "grade" => 0
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

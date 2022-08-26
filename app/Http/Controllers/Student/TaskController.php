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
            return $lesson->task;
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

        if($task->submissions()->where('taskable_id', $user->id)->first()){
            return response()->json([
                'status' => 'failed',
                "messaged" => 'You can only submit a task once',
            ], 400);
        }

        $user->submissions()->create([
            'link_to_resource' => $request->linkToResource,
            'submittable_id' => $task->id,
            'submittable_type' => 'App\\Models\\Task',
        ]);

        // TODO add notification for task submission

        return response()->json([
            'status' => 'success',
            'message' => 'Task submitted successfully',
        ], 201);
    }
}

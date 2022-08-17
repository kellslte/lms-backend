<?php

namespace App\Http\Controllers\Student;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskSubmissionRequest;

class TaskController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();

        return response()->json([
            'status' => 'success',
            'data' => [
                'completed_tasks' => $user->completedTasks(),
                'pending_tasks' => 8,
                'expired_tasks' => 4,
            ]
        ]);
    }

    public function submit(Task $task, TaskSubmissionRequest $request){
        $user = getAuthenticatedUser();

        if($task->running()){
            $user->submissions()->create([
                'link_to_resource' => $request->assignmentLink,
                'grade' => 0,
            ])->associate($task);

            return response()->json([
                'status' => 'success',
                'message' => 'Task submitted successfully',
            ], 201);
        }

       return (!$task->running()) ? response()->json([
        'status' => 'failed',
        'message' => 'You cannot submit this task anymore',
       ], 406) :  response()->json([
        'status' => 'error',
        'message' => 'Something went wrong here, please contact your facilitator',
       ], 404);
    }
}

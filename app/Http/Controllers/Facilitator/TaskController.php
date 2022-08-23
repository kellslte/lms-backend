<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Task;
use App\Models\User;
use App\Events\Graded;
use App\Models\Lesson;
use App\Events\TaskGraded;
use App\Events\TaskCreated;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;

class TaskController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();

        return response()->json([
            'published' => TaskService::getRunningTasks($user),
            'pending' => TaskService::getUnpublishedTasks($user),
            'graded' => TaskService::getTasksCompletedByStudents($user),
        ], 200);
    }


    public function store(CreateTaskRequest $request, Lesson $lesson){
        $user = getAuthenticatedUser();

        // check that the lesson exists on the course
        $course = $user->course;

        // load lessons
        $course->load('lessons');

        // check that the lesson exists on the course instance
        if($course->lessons->contains($lesson)){
            $task = $lesson->task()->create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'task_deadline_date' => $request ->taskDeadlineDate,
                'task_deadline_time' => $request ->taskDeadlineTime,
            ]);

            TaskCreated::dispatch($task);

            return response()->json([
                'status' => 'success',
                'message' => 'Task created successfully',
                'task' => $task
            ], 201);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Task could not be created',
        ], 400);
    }

    public function update(CreateTaskRequest $request, $task){
        $taskToUpdate = ($task) ? Task::find($task) : null;

        if(!is_null($taskToUpdate)){
            $taskToUpdate->update([
                "title" => $request->title,
                "description" => $request->description,
                "status" => $request->status,
                "task_deadline_date" => $request ->taskDeadlineDate,
                "task_deadline_time" => $request ->taskDeadlineTime,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Task updated successfully',
            ], 204);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Task could not be updated',
        ], 400);
    }

    public function viewSubmissions($task){

        $dbTask = (Task::find($task)) ? Task::find($task) : null ;

        if($dbTask){
            $submissions = $dbTask->submissions;
            if(!is_null($submissions)){
                return response()->json([
                    'status' => 'success',
                    'data' => $submissions,
                ]);
            }
        }

        return response()->json([
            'status' => 'failed',
            'data' => [],
        ], 404);
    }

    // TODO abstract logic for notifications to events

    public function gradeTask(Request $request, Task $task, User $student){
        // TODO get submission data for the task
       if(!is_null($task->submissions)){
            $submission = $task->submissions->taskable()->find($student->id);

            if(!is_null($submission)){
        
                // TODO grade the task and send a notification
                $submission->update([
                    'status' => 'approved',
                    'grade' => $request->grade
                ]);
        
                TaskGraded::dispatch($submission);
        
                return response()->json([
                    'status' => 'success',
                    'message' => 'Student task graded successfully'
                ], 204);
                
            }
       }

       return response()->json([
        'status' => 'failed',
        'message' => 'Something went wrong, please contact your administrator.',
       ], 400);
    }
}

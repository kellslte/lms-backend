<?php

namespace App\Http\Controllers\Facilitator;

use App\Http\Requests\EditTaskGradeRequest;
use App\Models\Task;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Services\TaskManager;
use App\Services\PointService;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Requests\CreateTaskRequest;

class TaskController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $user = getAuthenticatedUser();

        $response = TaskManager::taskStatus($user->course->id);

        if(array_key_exists("error", $response)){
            return response()->json([
                "status" => "error",
                "message" => "Task not found"
            ], 400);
        }

        return response()->json([
            "status" => "successful",
            "data" => $response
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
            $response = TaskManager::createTask([
                "title" => $request->title,
                "description" => $request->description,
                "deadline_date" => $request->taskDeadlineDate,
                "deadline_time" => $request->taskDeadlineTime,
                "status" => $request->status
            ], $lesson, $user->course->students);

            $code = ($response["status"])? 200 : 400;

            return response()->json($response, $code);
        }
    }

    public function update(CreateTaskRequest $request, $task){
        $taskToUpdate = ($task) ? Task::find($task) : null;

        if(!is_null($taskToUpdate)){
             $response = TaskManager::updateTask([
                "title" => $request->title,
                "description" => $request->description,
                "status" => $request->status,
                "deadline_date" => $request->taskDeadlineDate,
                "deadline_time" => $request->taskDeadlineTime,
            ], $taskToUpdate);

            $code = ($response["status"]) ? 200 : 400;

            return response()->json($response, $code);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Task could not be updated',
        ], 400);
    }

    public function viewSubmissions($task){
        $user =  getAuthenticatedUSer();

        $dbTask = Task::find($task);

        if($dbTask){
           $response = TaskManager::totalSubmissions($dbTask, $user->course->students);

           return response()->json([
            "data" => [
                "task" => new TaskResource($dbTask),
                "submissions" => [...$response]
            ]
           ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Task submissions could not be fetched',
        ], 404);
    }

    public function gradeTask(Request $request, Task $task, User $student){
        $request->validate([
            'grade' => 'required|numeric'
        ]);

        // TODO get submission data for the task
       $response = TaskManager::gradeTask($task, $student, (int)$request->grade);

       PointService::awardPoints($student, [
        "key" => "task_points",
        "points" => (int)$request->grade
       ]);

       return ($response)? response()->json([
        "status" => "successful",
        "message" => "Task has been graded"
       ]) : response()->json([
        "status" => "failed",
        "message" => "Task could not be successfully graded"
       ], 400);
    }

    public function markTaskAsGraded(Task $task): \Illuminate\Http\JsonResponse
    {
        $response = TaskManager::markAsGraded($task);

        return response()->json([
            "status" => $response["status"],
            "task" => $response["task"],
            "message" => $response["message"],
        ], $response["code"]);
    }

    public function closeSubmission(Task $task): \Illuminate\Http\JsonResponse
    {
        $response = TaskManager::closeTasksubmission($task);

        return response()->json([
            "message" => $response["message"],
            "task" => $response["task"],
            "status" => $response["status"]
        ], $response["code"]);
    }

    public function editSubmission(User $student, Task $task, EditTaskGradeRequest $request): \Illuminate\Http\JsonResponse
    {
        return TaskManager::editTaskGrade($student, $task,$request);
    }
}

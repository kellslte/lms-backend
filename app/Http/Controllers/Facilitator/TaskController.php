<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\Task;
use App\Models\User;
use App\Events\Graded;
use App\Models\Lesson;
use App\Events\TaskGraded;
use App\Events\TaskCreated;
use Illuminate\Http\Request;
use App\Services\TaskManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;

class TaskController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();

        return TaskManager::taskStatus($user->course->id);
    }


    public function store(CreateTaskRequest $request, Lesson $lesson){
        $user = getAuthenticatedUser();

        // check that the lesson exists on the course
        $course = $user->course;

        // load lessons
        $course->load('lessons');

        // check that the lesson exists on the course instance
        if($course->lessons->contains($lesson)){
            return TaskManager::createTask([
                "title" => $request->title,
                "description" => $request->description,
                "deadline_date" => $request->taskDeadlineDate,
                "deadline_time" => $request->taskDeadlineTime,
                "status" => $request->status
            ], $lesson, $user->course->students);
        }
    }

    public function update(CreateTaskRequest $request, $task){
        $taskToUpdate = ($task) ? Task::find($task) : null;

        if(!is_null($taskToUpdate)){
            return TaskManager::updateTask([
                "title" => $request->title,
                "description" => $request->description,
                "status" => $request->status,
                "deadline_date" => $request->taskDeadlineDate,
                "deadline_time" => $request->taskDeadlineTime,
            ], $taskToUpdate);
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
           return TaskManager::totalSubmissions($dbTask, $user->course->students);
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
       return TaskManager::gradeTask($task, $student, (int)$request->grade);
    }

    public function closeSubmission(Task $task){
        return TaskManager::closeTasksubmission($task);
    }
}

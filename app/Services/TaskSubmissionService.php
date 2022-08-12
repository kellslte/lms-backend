<?php
namespace App\Services;

class TaskSubmissionService {
    public static function submitTask($task){
        if($task->running()){
            
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'You cannot submit this task anymore because it has expired'
        ]);
    }

    public static function totalSubmissionsForTask($task){
        return $task->submissions->count();
    }

    public static function getTaskSubmissionsStats($task){
        
    }
}
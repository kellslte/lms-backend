<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();

        return response()->json([
            'status' => 'success',
            'data' => [
                'completed_tasks' => $user->completedTasks()
            ]
        ]);
    }
}

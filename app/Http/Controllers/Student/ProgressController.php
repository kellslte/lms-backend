<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ProgressService;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function getStudentProgress(){
        $lessons = ProgressService::upcomingLessons();

        return response()->json([
            "status" => "success",
            "data" => [
                "lessons" => $lessons
            ]
        ]);
    }
}

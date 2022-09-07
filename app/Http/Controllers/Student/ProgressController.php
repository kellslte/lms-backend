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

    public function incrementStudentProgress(Request $request, $lesson){
        $user = getAuthenticatedUser();

        $update = ProgressService::incrementStudentProgress($user, $request, $lesson);

        if($update){
            return response()->json([
                "status" => "success",
                "message" => "Lesson Updated Successfully",
            ]);
        }

        return response()->json([
            "status" => "failed",
            "message" => "Lesson Update Failed",
        ]); //
    }
}

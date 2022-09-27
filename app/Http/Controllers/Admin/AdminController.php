<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function updateCurriculum(Lesson $lesson){
     try {  $students = $lesson->course->students;
        $count = 0;

        foreach($students as $student){
            $student->load('curriculum', 'progress');

            $curriculum = $student->curriculum;
            $progress = $student->progress;

            $viewables = json_decode($curriculum->viewables, true);
            $courseProgress = json_decode($progress->course_progress, true);

            $courseProgress[] = [
                    "lesson_id" => $lesson->id,
                    "percentage" => 0
                ];

            $viewables[] = [
                "lesson_id" => $lesson->id,
                "lesson_status" => "uncompleted"
            ];

            $curriculum->update([
                "viewables" => json_encode($viewables),
            ]);

            $progress->update([
                "course_progress" => json_encode($courseProgress)
            ]);

            $count++;
            
        }
        return response()->json([
            "status" => "success",
            "message" => "Lesson details have been updated for each student and the loop ran {$count} times"
        ]);
    }catch(\Exception $e) {
        return response()->json([
            "status" => "error",
            "message" => $e->getMessage()
        ], 400);
    }
    }

    public function updateCourseContent(Request $request){
        $request->validate([
            "email" => "required|email",
        ]);

        $student = User::whereEmail($request->email);

        $course = $student->course;

        $lessons = $course->lessons;

        if(!$student->progress){
            $courseProgress = [];
            
            foreach($lessons as $lesson){
                $courseProgress[] = [
                    "lesson_id" => $lesson->id,
                    "percentage" => 0
                ];
            }
            
            $student->progress()->create([
                "course" => $course->title,
                "course_progress" => json_encode($courseProgress)
            ]);
        }

        if(!$student->curriculum){
            $curriculum = [];

            foreach($lessons as $lesson){
                $curriculum[] = [
                    "lesson_id" => $lesson->id,
                    "lesson_status" => "uncompleted"
                ];
            }

            $student->curriculum()->create([
                "viewables" => json_encode($curriculum)
            ]);
        }

        return $student;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function updateCurriculum(Lesson $lesson){
        $students = $lesson->course->students;

        foreach($students as $student){
            $curriculum = $student->curriculum;
            $progress = $student->progress;

            $viewables = collect(json_decode($curriculum->viewables, true));
            $courseProgress = collect(json_decode($progress->course_progress, true));

            $courseProgress->merge([
                "course_progress" => [
                    "lesson_id" => $lesson->id,
                    "percentage" => 0
                ]
            ]);

            $viewables->merge([
                "lesson_id" => $lesson->id,
                "lesson_status" => "uncompleted"
            ]);

            $curriculum->update([
                "viewables" => json_encode($viewables),
            ]);

            $progress->update([
                "course_progress" => json_encode($courseProgress)
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\TaskManager;
use App\Http\Controllers\Controller;

class ClassroomController extends Controller
{
    public function index(){
        $courses = Course::all();

        $lessons = collect($courses)->map(function ($course) {

            $lessonsData = collect($course->lessons)->map(function($lesson) {
                return [
                    "title" => $lesson->title,
                    "description" => $lesson->description,
                    "media" => $lesson->media,
                    "status" => $lesson->status,
                    "date_published" => formatDate($lesson->created_at),
                    "tutor" => $lesson->course->facilitator->name,
                    "views" => $lesson->views->count,
                    "task_submissions" => TaskManager::getSubmissions($lesson->tasks, $lesson->course->students)->count()
                ];
            });

            return [
                "course" => $course->title,
                "lessons" => $lessonsData,
                "enrolled_students" => $course->students->count(),
            ];
        });


        return response()->json([
            "status" => "success",
            "data" => [
                "lessons" => $lessons
            ]
        ]);
    }
}

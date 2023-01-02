<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\TaskManager;
use App\Http\Controllers\Controller;

class ClassroomController extends Controller
{
    public function index()
    {
        $courses = Course::all();

        $lessonsResponse = collect($courses)->map(function ($course) {

            $lessonsData = collect($course->lessons)->map(function ($lesson) {
                return [
                    "title" => $lesson->title,
                    "description" => $lesson->description,
                    "media" => $lesson->media,
                    "status" => $lesson->status,
                    "date_published" => formatDate($lesson->created_at),
                    "tutor" => $lesson->course->facilitator->name,
                    "views" => 0,
                    "task_submissions" => TaskManager::getSubmissions($lesson->tasks, $lesson->course->students)->count()
                ];
            });

            return [
                "course_id" => $course->id,
                "course" => $course->title,
                "lessons" => $lessonsData,
                "enrolled_students" => $course->students->count(),
            ];
        });


        return response()->json([
            "success" => true,
            "message" => "Courses data retrieved successfully",
            "data" => [
                "response" => $lessonsResponse
            ]
        ]);
    }

    public function search(Request $request)
    {
        $course_id = $request->course;

        $course = Course::find($course_id);

        if (!$course) {
            return response()->json([
                "success" => false,
                "error_code" => 404,
                "message" => "The requested resource was not found",
                "data" => []
            ], 404);
        }

        $response = collect($course->lessons)->map(function ($lesson) use ($course) {
            return [
                "course" => $course->title,
                "lessons" => [
                    "title" => $lesson->title,
                    "description" => $lesson->description,
                    "media" => $lesson->media,
                    "status" => $lesson->status,
                    "date_published" => formatDate($lesson->created_at),
                    "tutor" => $lesson->course->facilitator->name,
                    "views" => $lesson->views->count,
                    "task_submissions" => TaskManager::getSubmissions($lesson->tasks, $lesson->course->students)->count()
                ],
                "enrolled_students" => $course->students->count()
            ];
        });

        return response()->json([
            "success" => true,
            "message" => "Course data successfully retrieved",
            "data" => [
                "response" => $response,
            ]
        ], 200);
    }
}
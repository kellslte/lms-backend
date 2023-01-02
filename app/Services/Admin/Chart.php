<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class Chart
{
    public static function render(Collection $courses)
    {

        $response = [];

        foreach ($courses as $course) {
            $response[] = self::getCoursePerformance($course);
        }

        return $response;
    }

    private static function getCoursePerformance($course)
    {
        $students = $course->students;

        $students->load("submissions");

        $lessons = $course->lessons;

        $taskSubmissions = collect($students)->map(function ($student) {
            return json_decode($student->submissions->tasks, true);
        });

        $lessonViews = collect($lessons)->map(function ($lesson) {
            return [
                "lesson_id" => $lesson->id,
                "views" => $lesson->views,
            ];
        })->count();

        $taskSubmissionCount = collect($taskSubmissions)->map(function ($task) use ($lessons) {
            return collect($lessons)->map(function ($lesson) use ($task) {
                $tasks = $lesson->tasks;
                return collect($tasks)->map(function ($item) use ($task) {
                    if (collect($task)->where($item)->first()) {
                        return collect($task)->where("id", $item->id)->first();
                    }
                });
            })->count();
        })->sum();


        return [
            "course" => $course->title,
            "lesson_views" => $taskSubmissionCount,
            "task_submission_count" => $taskSubmissionCount,
        ];
    }
}
<?php

namespace App\Services;

use App\Notifications\NotifyStudentWhenLessonCreated;
use Illuminate\Support\Facades\Notification;

class StudentService
{
    public static function execute($students, $lesson)
    {
        info("Students event has been fired");
        // update students progress
        self::updateProgress($students, $lesson);
        // update students curriculum
        self::updateCurriculum($students, $lesson);
        // notify students of lesson upload
        self::notifyStudents($students);
        info("Students event has stopped firing");
    }

    private static function updateProgress($students, $lesson)
    {
        foreach ($students as $student) {
            $progress = $student->progress;

            $courseProgress = json_decode($progress->course_progress, true);

            $progress->course_progress = json_encode([...$courseProgress, [
                "lesson_id" => $lesson->id,
                "percentage" => 0
            ]]);

            $progress->save();
        }
    }

    private static function updateCurriculum($students, $lesson)
    {
        foreach ($students as $student) {
            $curriculum = $student->curriculum;

            $courseCurriculum = json_decode($curriculum->viewables, true);

            $curriculum->viewables = json_encode([...$courseCurriculum, [
                "lesson_id" => $lesson->id,
                "lesson_status" => "uncompleted"
            ]]);

            $curriculum->save();
        }
    }

    private static function notifyStudents($students)
    {
        foreach ($students as $student) {
            Notification::send($student, new NotifyStudentWhenLessonCreated());
        }
    }
}

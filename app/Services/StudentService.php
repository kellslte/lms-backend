<?php
namespace App\Services;

use App\Notifications\NotifyStudentWhenLessonCreated;

class StudentService {
    public static function execute($students, $lesson){
        // update students progress
        $progress = self::updateProgress($students, $lesson);

        // update students curriculum
        $curriculum = self::updateCurriculum($students, $lesson);

        // notify students of lesson upload
        $notification = self::notifyStudents($students);

        // return response
        if($progress && $curriculum && $notification){
            return true;
        }
        return false;
    }
    
    private static function updateProgress($students, $lesson){
        $count = 0;
        foreach($students as $student){
            $progress = $student->progress;

            $courseProgress = json_decode($progress->course_progress, true);

            array_push($courseProgress, [
                "lesson_id" => $lesson->id,
                "percentage" => 0
            ]);

            $progress->update([
                "course_progress" => json_encode($courseProgress)
            ]);
            $count++;
        }
        if ($count === $students->count()) {
            return true;
        }   
        return false;
    }

    private static function updateCurriculum($students, $lesson){
        $count = 0;
        foreach ($students as $student) {
            $curriculum = $student->curriculum;

            $courseCurriculum = json_decode($curriculum->viewables, true);

            array_push($courseCurriculum, [
                "lesson_id" => $lesson->id,
                "lesson_status" => "uncompleted"
            ]);

            $curriculum->update([
                "viewables" => json_encode($courseCurriculum)
            ]);
            $count++;
        }
        if ($count === $students->count()) {
            return true;
        }
        return false;
    }

    private static function notifyStudents($students){
        $count = 0;
        foreach ($students as $student) {
            $student->notify(new NotifyStudentWhenLessonCreated());
            $count++;
        }
        if($count === $students->count()){
            return true;
        }

        return false;
    }
}
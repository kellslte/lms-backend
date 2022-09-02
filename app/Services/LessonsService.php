<?php
namespace App\Services;

use App\Models\Lesson;
use App\Models\Facilitator;
use App\Services\TaskService;
use App\Http\Requests\CreateLessonRequest;

class LessonsService {

    public static function getAllLessons(Facilitator $user){
        return collect($user->course->lessons)->map(function($lesson){
            return [
                "id" => $lesson->id,
                'title' => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "tutor" => $lesson->course->facilitator->name,
                "student_views" => $lesson->views->views,
                "task_submitted" => TaskService::getSubmissions($lesson->task)
            ];
        });
    }

    public static function getPublishedLessons(Facilitator $user){
        return collect($user->course->lessons)->reject(fn($lesson) => $lesson->status === 'unpublished')->map(function($lesson){
            return $lesson->load('views');
        });
    }

    public static function getUnpublishedLessons(Facilitator $user){
        return collect($user->course->lessons)->reject(fn($lesson) => $lesson->status === 'published');
    }

    public static function createLesson(CreateLessonRequest $request){
        // TODO first upload the video then return the id

        // TODO create the lessons and attatch the video id as a media

        // TODO if lesson has a transcript upload the transcript to storage then put the link to the transcript as a media

        // TODO if the lesson has external resources then create the lesson resource too

        // TODO return json response once transaction is done
    }

    public static function getUserCurriculum($user){
        return collect(json_decode($user->curriculum->viewables))->map(function ($lesson) {
            $lesson = Lesson::find($lesson->lesson_id);

            return [
                "title" => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "media" => $lesson->media,
                "status" => $lesson->status,
            ];
        });
    }

    public static function getClassroomData($user){
        return collect(json_decode($user->curriculum->viewables))->map(function ($lesson) {
            $lesson = Lesson::find($lesson->lesson_id);

            return [
                "id" => $lesson->id,
                "title" => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "status" => $lesson->status,
                "media" => $lesson->media
            ];
        });
    }
}
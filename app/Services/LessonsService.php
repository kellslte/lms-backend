<?php
namespace App\Services;

use App\Models\Lesson;
use App\Models\Facilitator;
use App\Http\Requests\CreateLessonRequest;

class LessonsService {
    protected $course;

    public function __construct(Facilitator $user){
        $this->course = $user->course;
    }

    public function getAllLessons(){
        return $this->course->lessons;
    }

    public function getPublishedLessons(){
        return collect($this->course->lessons)->reject(fn($lesson) => $lesson->status === 'unpublished');
    }

    public function getUnpublishedLessons(){
        return collect($this->course->lessons)->reject(fn($lesson) => $lesson->status === 'published');
    }

    public function createLesson(CreateLessonRequest $request){
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
                "media" => $lesson->media
            ];
        });
    }

    public static function getClassroomData($user){
        return collect(json_decode($user->curriculum->viewables))->map(function ($lesson) {
            $lesson = Lesson::find($lesson->lesson_id);

            return [
                "title" => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "status" => $lesson->status,
                "media" => $lesson->media
            ];
        })->groupBy('published_date');
    }
}
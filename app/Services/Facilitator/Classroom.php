<?php
namespace App\Services\Facilitator;

use App\Actions\Curriculum\UpdateCurriculum;
use App\Actions\Lesson\CreateLesson;
use App\Models\Lesson;
use App\Models\Facilitator;
use App\Actions\Lesson\GetLessons;
use App\Actions\Lesson\UploadLessonMedia;

class Classroom{
    public static function getLessons(Facilitator $user){
        //$lessons  =  $user->course->lessons;

        $lessons  =  Lesson::where("tutor", $user->name)->get();

        $published = $lessons->reject(fn ($lesson) => $lesson->status !== "published");

        $unpublished = $lessons->reject(fn ($lesson) => $lesson->status !== "unpublished");

        return GetLessons::handle($published, $unpublished, $user);
    }

    public static function getLesson(Lesson $lesson){
        return [
            "id" => $lesson->id,
            "title" => $lesson->title,
            "description" => $lesson->description
        ];
    }

    public static function createLesson($request){
        // upload lesson media
        $media = UploadLessonMedia::handle($request);

        // create lesson record
        $lesson = CreateLesson::handle($request, getAuthenticatedUser()->name, $media);

        // update course curriculum - use an observer?
        UpdateCurriculum::handle($lesson);

        // return lesson
        return [
            "lesson" => $lesson,
            "media" => [
                "video" => $lesson->media->video_link,
                "thumbnail" => $lesson->media->thumbnail
            ],
            "resources" => collect($lesson->resources)->map(fn($resource) => $resource->link )
        ];
    }

    public static function editLesson($request, $lesson){
        // $lesson->update([
        //     "title" =>
        // ]);
    }

    public static function deleteLesson(){}
}
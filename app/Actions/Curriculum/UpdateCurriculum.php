<?php
namespace App\Actions\Curriculum;

class UpdateCurriculum{
    public static function handle($lesson){
        $curriculum = $lesson->course->curriculum;

        $plan = json_decode($curriculum->plan, true);

        $plan[] = [
            "upload_date" => today(),
            "lesson_id" => $lesson->id,
            "lesson_title" => $lesson->title,
            "lesson_description" => $lesson->description,
            "video" => $lesson->media->video_link,
            "thumbnail" => $lesson->media->thumbnail,
            "resources" => $lesson->resources,
            "views" => $lesson->views,
        ];

        $curriculum->plan = $plan;

        $curriculum->save();
    }
}

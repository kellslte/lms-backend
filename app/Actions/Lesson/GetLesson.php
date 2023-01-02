<?php
namespace App\Actions\Lesson;

use App\Models\Lesson;

class GetLesson{
    public static function handle(Lesson $lesson){
        return [
            "id" => $lesson->id,
            "title" => $lesson->title,
            "description" => $lesson->description,
            "thumbnail" => $lesson->media->thumbnail,
            "video" => $lesson->media->video_link,
            "resources" => $lesson->resources
        ];
    }
}

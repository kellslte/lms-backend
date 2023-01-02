<?php
namespace App\Actions\Lesson;

use App\Models\Lesson;
use Illuminate\Http\Request;

class CreateLesson {
    public static function handle(Request $request, string $tutor, Array $media){
        $lesson = Lesson::create([
            "title" => $request->title,
            "description" => $request->description,
            "tutor" => $tutor,
        ]);

        if($request->has('recourses')){
            $resources = explode(",", $request->resources);

            foreach ($resources as $resource) {
                $lesson->resources()->create([
                    "link" => preg_replace('/\"/i', '', str_replace('"', '', $resource))
                ]);
            }
        }

        $lesson->media()->create([
            "video_link" => $media["video_url"],
            "thumbnail" => $media["thumbnail_url"],
            "videoPath" => "",
            "thumbnailPath" => "",
            "youtube_video_id" => ""
        ]);

        return $lesson;
    }
}
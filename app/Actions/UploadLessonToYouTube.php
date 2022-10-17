<?php

namespace App\Actions;


use App\Models\Course;
use App\Services\YoutubeService;
use Illuminate\Http\Request;

class UploadLessonToYouTube
{
    public static function handle(Request $request){
        $service = app(YoutubeService::class);

        return $service->uploadVideoToPlaylist([
            "courseTitle" => Course::whereTitle($request->track),
            "title" => $request->title,
            "description" => $request->description,
            "tags" => $request->title,
            "lessonVideo" => $request->file('lessonVideo'),
            "lessonThumbnail" => $request->file('lessonThumbnail')
        ]);
    }
}

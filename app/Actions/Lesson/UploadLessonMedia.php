<?php

namespace App\Actions\Lesson;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadLessonMedia
{
    public static function handle(Request $request): array
    {
        // upload file to server
        $videoPath = Storage::disk('local')->put("lessons", $request->file('lessonVideo'));

        // upload lesson thumbnail to server
        $thumbnail = $request->file('lessonThumbnail')->store('thumbnails', 'public');

        return [
            'video_url' => asset("uploads/".$videoPath),
            'thumbnail_url' => asset("uploads/".$thumbnail)
        ];
    }
}
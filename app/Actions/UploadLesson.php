<?php

namespace App\Actions;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadLesson
{
    public static function handle(Request $request): array
    {
        // upload file to server
        $videoPath = Storage::disk('s3')->put("uploads", $request->file('lessonVideo'));

        $transcriptUrl = "";

        // upload transcript to server
        if($request->file("lessonTranscript")){
            $transcript = $request->file('lessonTranscript')->store('transcripts', 'public');
        }

        // upload lesson thumbnail to server
        $thumbnail = $request->file('lessonThumbnail')->store('thumbnails', 'public');;

        return [
            'video_path' => "",
            'video_url' => Storage::disk('s3')->url($videoPath),
            'transcript' => "",
            'transcript_url' => "",
            'thumbnail_path' => $thumbnail,
            'thumbnail_url' => asset("uploads/".$thumbnail)
        ];
    }
}

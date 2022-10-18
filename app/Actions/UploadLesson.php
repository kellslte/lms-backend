<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadLesson
{
    public static function handle(Request $request): array
    {
        // upload file to server
        $video = cloudinary()->upload($request->file('lessonVideo')->getRealPath())->getSecurePath();

        $transcriptUrl = "";

        // upload transcript to server
        if($request->file("lessonTranscript")){
            $transcript = $request->file('lessonTranscript')->store("/transcripts", "public");
            $transcriptUrl = asset("/uploads/{$transcript}");
        }

        // upload lesson thumbnail to server
        $thumbnail = $request->file('lessonThumbnail')->store("/thumbnails", "public");
        $thumbnailUrl = asset("/uploads/{$thumbnail}");

        return [
            'video_path' => "",
            'video_url' => $video,
            'transcript' => $transcript ?? Storage::path($transcript),
            'transcript_url' => $transcriptUrl,
            'thumbnail_path' => $thumbnail,
            'thumbnail_url' => $thumbnailUrl
        ];
    }
}

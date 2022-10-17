<?php
namespace App\Services;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProcessVideo{
    private static function getVideoInfo($path): string
    {
         return Storage::path($path);
    }

    private static function getVideoThumbnail($path): string
    {
        return Storage::path($path);
    }

    private static function cleanUp($videoPath, $thumbnailPath): void
    {
        // delete the files on the server on successful upload
        Storage::delete([
            $videoPath,
            $thumbnailPath
        ]);
    }

    /**
     * @throws \Exception
     */
    public static function execute(Lesson $lesson): void
    {
        $videoFile = self::getVideoInfo($lesson->media->videoPath);

        $thumbnailFile = self::getVideoThumbnail($lesson->media->thumbnailPath);

        // add fields to the request instance
        $request = [
            "title" => $lesson->title,
            "description" => $lesson->description,
            "tags" => $lesson->title,
            "lessonVideo" => $videoFile,
            "lessonThumbnail" => $thumbnailFile,
            "courseTitle" => $lesson->course->title,
        ];

        // attempt file upload
        try{
            // upload files and return response
            $response = getYoutubeVideoDetails($request);

            // update lesson details
            $lesson->media->update([
                "video_link" => $response["videoLink"],
                "thumbnail" => $response["thumbnail"],
                "youtube_video_id" => $response["youtube_video_id"],
            ]);

            // delete the files from the server
            self::cleanUp($lesson->media->videoPath, $lesson->media->thubnailPath);
        }
        catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}

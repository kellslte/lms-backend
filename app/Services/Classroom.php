<?php
namespace App\Services;

use App\Actions\GetLessons;
use App\Actions\UploadLesson;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Facilitator;


class Classroom {
    public static function allLessons(Facilitator $user): array
    {
        $publishedLessons = [];
        $unpublishedLessons = [];

        try {
            $lessons  =  Lesson::where("tutor", $user->name)->get();

            $published = $lessons->reject(fn($lesson) => $lesson->status !== "published");

            $unpublished = $lessons->reject(fn($lesson) => $lesson->status !== "unpublished");

            return GetLessons::handle($published, $unpublished, $user);
        } catch (\Throwable $th) {
            return [
                "published_lessons" => [],
                "unpublished_lessons" => [],
                "error" => $th->getMessage()
            ];
        }
    }

    public static function save($request, Course $course, string $tutor): array
    {
        // upload lesson media
        $response = UploadLesson::handle($request);

        // create lesson record in database
        $lesson = $course->lessons()->create([
            "title" => $request->title,
            "description" => $request->description,
            "tutor" => $tutor,
        ]);

        $lesson->media()->create([
            "video_link" => $response["video_url"],
            "videoPath" => "",
            "thumbnail" => $response["thumbnail_url"],
            "thumbnailPath" => "",
            "transcript" => $response['transcript_url'],
            "youtube_video_id" => ""
        ]);

//        get lesson resources and convert to array
        if ($request->has('resources')) {
            $resources = explode(",", $request->resources);

            foreach ($resources as $resource) {
                $lesson->resources()->create([
                    "link" => preg_replace('/\"/i', '', str_replace('"', '', $resource))
                ]);
            }
        }

        $lesson->views()->create();

        return  ["lesson" => $lesson];
    }

    public static function updateLesson($request, $lesson): \Illuminate\Http\JsonResponse
    {
        try {
            if($lesson->status === "unpublished"){
                $videoPath = $lesson->media->video_link;
                $thumbnail = $lesson->media->thumbnail;
                $transcript = $lesson->transcript;

                $request->merge([
                    "tags" => $lesson->title,
                    "lessonVideo" => $videoPath,
                    "lessonThumbnail" => $thumbnail
                ]);

                $videoLink = getYoutubeVideoDetails($request);


            }

            $lesson->update([
                "title" => $request->title,
                "description" => $request->description,
                "status" => $request->status
            ]);

            $request->merge([
                "tags" => $lesson->course->title,
                "videoId" => $lesson->media->youtube_video_id,
            ]);

            if ($request->file('lessonVideo')) {
                $response = updateYoutubeVideoDetails($request);
            }

            if ($response) {
                return response()->json([
                    "status" => "success",
                    "message" => "Your video has been updated successfully",
                    "data" => [
                        "response" => $response
                    ]
                ], 200);
            }

            return response()->json([
                "status" => "success",
                "message" => "Your lesson has been updated successfully",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => "error",
                'message' => "An error has occurred while updating the lesson.",
            ], 400);
        }
    }
}

<?php
namespace App\Services;

use App\Actions\GetLessons;
use App\Actions\UploadLesson;
use App\Actions\UploadLessonToYouTube;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Facilitator;
use Illuminate\Support\Str;
use App\Events\LessonCreated;
use App\Services\StudentService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotifyStudentWhenLessonCreated;
use Spatie\SlackAlerts\Facades\SlackAlert;


class Classroom {
    public static function allLessons(Facilitator $user): array
    {
        $publishedLessons = [];
        $unpublishedLessons = [];

        try {
            $lessons  =  Lesson::where("tutor", $user->name)->flatten();

            $published = $lessons->reject(fn($lesson) => $lesson->status !== "published");

            $unpublished = $lessons->reject(fn($lesson) => $lesson->status !== "unpublished");

            return GetLessons::handle($published, $unpublished);
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

        foreach ($course->students as $student) {
            // update progress
            $progress = $student->progress;

            $courseProgress = json_decode($progress->course_progress, true);

            $progress->course_progress = json_encode([...$courseProgress, [
                "lesson_id" => $lesson->id,
                "percentage" => 0
            ]]);

            $progress->save();

            // update curriculum
            $curriculum = $student->curriculum;

            $courseCurriculum = json_decode($curriculum->viewables, true);

            $curriculum->viewables = json_encode([...$courseCurriculum, [
                "lesson_id" => $lesson->id,
                "lesson_status" => "uncompleted"
            ]]);

            $curriculum->save();
        }

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

        SlackAlert::message("A new lesson has been uploaded in the {$lesson->course->title} track!");

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

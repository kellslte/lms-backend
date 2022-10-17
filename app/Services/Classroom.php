<?php
namespace App\Services;

use App\Actions\GetLessons;
use App\Actions\UploadLessonToYouTube;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Facilitator;
use Illuminate\Support\Str;
use App\Events\LessonCreated;
use App\Services\StudentService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotifyStudentWhenLessonCreated;


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

    public static function save($request, $course, string $tutor): array
    {
//        // upload file to server
        $video = $request->file('lessonVideo')->store("/lessons", "public");
        $videoUrl = asset("/uploads/{$video}");

        $transcriptUrl = "";

        // upload transcript to server
        if($request->file("lessonTranscript")){
            $transcript = $request->file('lessonTranscript')->store("/transcripts", "public");
            $transcriptUrl = asset("/uploads/{$transcript}");
        }

        // upload lesson thumbnail to server
        $thumbnail = $request->file('lessonThumbnail')->store("/thumbnails", "public");
        $thumbnailUrl = asset("/uploads/{$thumbnail}");
//        $response = UploadLessonToYouTube::handle($request);

        $lesson = $course->lessons()->create([
            "title" => $request->title,
            "description" => $request->description,
            "tutor" => $tutor,
        ]);

        $lesson->media()->create([
            "video_link" => $videoUrl,
            "videoPath" => $video,
            "thumbnail" => $thumbnailUrl,
            "thumbnailPath" => $thumbnail,
            "transcript" => $transcriptUrl,
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

            // Send notification to students
            Notification::send($student, new NotifyStudentWhenLessonCreated());
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

        return  ["lesson" => $lesson, "resources" =>  json_encode($request->resources)];
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

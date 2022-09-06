<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\TaskService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CreateLessonRequest;

class LessonsService {

    public static function getAllLessons($user){
        return collect($user->course->lessons)->map(function($lesson){
            return [
                "id" => $lesson->id,
                'title' => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "tutor" => $lesson->tutor,
                "student_views" => $lesson->views->views,
                "task_submitted" => TaskService::getSubmissions($lesson->task)
            ];
        });
    }

    public static function getPublishedLessons($user){
        return collect($user->course->lessons)->reject(fn($lesson) => $lesson->status === 'unpublished')->map(function($lesson){
            return $lesson->load('views');
        });
    }

    public static function getUnpublishedLessons($user){
        return collect($user->course->lessons)->reject(fn($lesson) => $lesson->status === 'published');
    }

    public static function createLesson($request, $user){
        $course = Course::firstWhere("title", $user->course->title);

        // create lesson
        $lesson = $course->lessons()->create([
            "title" => $request->title,
            "description" => $request->description,
            "tutor" => $user->name,
        ]);

        $request->merge([
            "tags" => $course->title
        ]);

        // upload video by calling youtube service
        $youtubeVideoDetails = getYoutubeVideoDetails($request);

        // upload transacript
        $tanscriptUrl = $request->file("lessonTranscript")->store("/" . $user->id, "public");

        // TODO: send notification that a new lesson has been uploaded


        // use returned video details to create video resource
        $lesson->media()->create([
            "video_link" => $youtubeVideoDetails["videoLink"],
            "thumbnail" => $youtubeVideoDetails["thumbnail"],
            "transcript" => Storage::url($tanscriptUrl),
            "youtube_video_id" => $youtubeVideoDetails["videoId"],
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Your lesson has been created successfully.",
            "data" => [
                "lesson" => $lesson
            ],
        ]);
        try {
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Something happened and your lesson could not be created"
            ]);
        }
    }

    public static function updateLesson($request, $lesson){
        try {
            $lesson->update([
                "title" => $request->title,
                "description" => $request->description,
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

    public static function getUserCurriculum($user){
        return collect(json_decode($user->curriculum->viewables))->map(function ($viweable) {
            $lesson = Lesson::find($viweable->lesson_id);

            return ($viweable->lesson_status === "uncompleted")? [
                "title" => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "media" => $lesson->media,
                "status" => $lesson->status,
            ]: null;
        })->filter();
    }

    public static function getClassroomData($user){
        return collect(json_decode($user->curriculum->viewables))->map(function ($lesson) {
            $lesson = Lesson::find($lesson->lesson_id);

            return [
                "id" => $lesson->id,
                "title" => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "status" => $lesson->status,
                "media" => $lesson->media,
                "views" => json_decode($lesson->views, true),
            ];
        })->groupBy(fn ($val) => Carbon::parse($val->updated_at)->format('D'));
    }

    public static function getUpcoming(){}

    public static function deleteLesson($lesson){
        // video from YouTube
        (new YouTubeService)->deleteVideo($lesson->media->youtube_video_id);

        // delete lesson from database
        return  $lesson->delete();
    }
}
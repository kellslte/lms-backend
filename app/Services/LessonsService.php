<?php
namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Facilitator;
use App\Services\TaskService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CreateLessonRequest;

class LessonsService {

    public static function getAllLessons(Facilitator $user){
        return collect($user->course->lessons)->map(function($lesson){
            return [
                "id" => $lesson->id,
                'title' => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "tutor" => $lesson->course->facilitator->name,
                "student_views" => $lesson->views->views,
                "task_submitted" => TaskService::getSubmissions($lesson->task)
            ];
        });
    }

    public static function getPublishedLessons(Facilitator $user){
        return collect($user->course->lessons)->reject(fn($lesson) => $lesson->status === 'unpublished')->map(function($lesson){
            return $lesson->load('views');
        });
    }

    public static function getUnpublishedLessons(Facilitator $user){
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

    public static function updateLesson($request, $user, $lesson){

    }

    public static function getUserCurriculum($user){
        return collect(json_decode($user->curriculum->viewables))->map(function ($lesson) {
            $lesson = Lesson::find($lesson->lesson_id);

            return [
                "title" => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "media" => $lesson->media,
                "status" => $lesson->status,
            ];
        });
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
                "media" => $lesson->media
            ];
        });
    }
}
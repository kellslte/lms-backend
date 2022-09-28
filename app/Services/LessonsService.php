<?php
namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;

class LessonsService {

    public static function getAllLessons($user){

        return collect($user->course->lessons)->map(function($lesson) use ($user){
            return [
                "id" => $lesson->id,
                'title' => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "tutor" => $lesson->tutor,
                "student_views" => $lesson->views->views,
                "task_submitted" => TaskManager::getSubmissions($lesson->task, $user->course->students)
            ];
        });
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
        $progress = collect(json_decode($user->progress->course_progress, true));

        return collect(json_decode($user->curriculum->viewables, true))->map(function ($viewables) use ($progress) {
            $lesson = Lesson::find($viewables["lesson_id"]);
            $lessonProgress = $progress->where("lesson_id", $viewables["lesson_id"])->first();
            
            if(!is_null($lesson) && !is_null($lessonProgress)){

                return ($viewables["lesson_status"] === "uncompleted") ? [
                    "title" => $lesson->title,
                    "description" => $lesson->description,
                    "published_date" => formatDate($lesson->updated_at),
                    "media" => $lesson->media,
                    "status" => $lesson->status,
                    "tutor" => $lesson->course->facilitator->name,
                    "percentageWatched" => $lessonProgress["percentage"]
                ]: null;
            }

        })->filter();
    }

    public static function getClassroomData($user){
        $progress = collect(json_decode($user->progress->course_progress, true));
        
        return collect(json_decode($user->curriculum->viewables, true))->map(function ($lesson) use($progress) {
             $lessonProgress = $progress->where("lesson_id", $lesson["lesson_id"])->first();

            $lesson = Lesson::all()->where("id", $lesson["lesson_id"])->first();

                if(!is_null($lesson) && !is_null($lessonProgress)){
                    return ($lessonProgress["percentage"] === 100) ? [
                        "id" => $lesson->id,
                        "title" => $lesson->title,
                        "description" => $lesson->description,
                        "published_date" => formatDate($lesson->updated_at),
                        "status" => "completed",
                        "media" => $lesson->media,
                        "tutor" => $lesson->course->facilitator->name,
                        "percentage" => $lessonProgress["percentage"]
                    ]: [
                        "id" => $lesson->id,
                        "title" => $lesson->title,
                        "description" => $lesson->description,
                        "published_date" => formatDate($lesson->updated_at),
                        "status" => "uncompleted",
                        "media" => $lesson->media,
                        "tutor" => $lesson->course->facilitator->name,
                        "percentage" => $lessonProgress["percentage"]
                    ];
                } 


            return [];
        })->filter() ?? [];
    }

    public static function getUpcoming(){}

    public static function deleteLesson($lesson){
        // video from YouTube
        (new YouTubeService)->deleteVideo($lesson->media->youtube_video_id);

        // delete lesson from database
        return  $lesson->delete();
    }

    public function lessonViews($user){
        $lessons = $user->course->lessons->orderDesc()->take(2);       
    }

    // Facilitator Methods
    public static function myLessons(){
        $lessons = getAuthenticatedUser()->lessons();
        $lessons->load('tasks');

        $myLessons = collect($lessons)->sortBy('updated_at');
        
    }
}
<?php
namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Events\LessonCreated;
use App\Services\TaskService;
use Illuminate\Support\Facades\Storage;

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

    public static function createLesson($request, $user){
        $course = Course::firstWhere("title", $user->course->title);
        
        $request->merge([
            "tags" => $course->title,
            "courseTitle" => $course->title
        ]);

        try {
        // upload video by calling youtube service
        if
        (
            $youtubeVideoDetails = getYoutubeVideoDetails($request)["response"]
        ){
            // upload transacript
            $tanscriptUrl = $request->file("lessonTranscript")->store("/transcripts", "public");
            
            // create lesson
            $lesson = $course->lessons()->create([
                "title" => $request->title,
                "description" => $request->description,
                "tutor" => $user->name,
            ]);

            // use returned video details to create video resource
            $lesson->media()->create([
                "video_link" => $youtubeVideoDetails["videoLink"],
                "thumbnail" => $youtubeVideoDetails["thumbnail"],
                "transcript" => Storage::url($tanscriptUrl),
                "youtube_video_id" => $youtubeVideoDetails["youtube_video_id"],
            ]);
            
            $resources = $request->resources;
            
            foreach($resources as $resource){
                $lesson->resources()->create($resource);
            }

            
            // TODO: send notification that a new lesson has been uploaded
            LessonCreated::dispatch($course->students);
    
            // TODO add lesson to students curriculum
            
            // TODO add lesson to student progress 
            
            // TODO create lesson views table
        }
        
        
        return response()->json([
            "status" => "success",
            "message" => "Your lesson has been created successfully.",
            "data" => [
                "lesson" => $lesson
            ],
        ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Something happened and your lesson could not be created"
            ]);
        }
    }

    public static function saveDraft($request, $user){
        $course = Course::firstWhere("title", $user->course->title);

        // create lesson
        $lesson = $course->lessons()->create([
            "title" => $request->title,
            "description" => $request->description,
            "tutor" => $user->name,
            "published" => "unpublished",
        ]);

        $request->merge([
            "tags" => $course->title
        ]);

        // upload video by calling youtube service
        $youtubeVideoDetails = getYoutubeVideoDetails($request);

        // upload transacript
        $tanscriptUrl = $request->file("lessonTranscript")->store("/" . $user->id, "public");

        // use returned video details to create video resource
        $lesson->media()->create([
            "video_link" => $youtubeVideoDetails["videoLink"],
            "thumbnail" => $youtubeVideoDetails["thumbnail"],
            "transcript" => Storage::url($tanscriptUrl),
            "youtube_video_id" => $youtubeVideoDetails["videoId"],
        ]);

        $lesson->resources()->create($request->resources);

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
        $progress = collect(json_decode($user->progress->course_progress, true));

        return collect(json_decode($user->curriculum->viewables, true))->map(function ($viweable) use ($progress) {
            $lesson = Lesson::find($viweable->lesson_id);
            $lessonProgress = $progress->where("lesson_id", $viweable->lesson_id)->first();

            return ($viweable->lesson_status === "uncompleted")? [
                "title" => $lesson->title,
                "description" => $lesson->description,
                "published_date" => formatDate($lesson->updated_at),
                "media" => $lesson->media,
                "status" => $lesson->status,
                "tutor" => $lesson->course->facilitator->name,
                "percentageWatched" => $lessonProgress["percentage"]
            ]: null;
        })->filter();
    }

    public static function getClassroomData($user){
        $progress = collect(json_decode($user->progress->course_progress, true));

        return collect(json_decode($user->curriculum->viewables, true))->map(function ($lesson) use($progress) {
            $lessonProgress = $progress->where("lesson_id", $lesson["lesson_id"])->first();
            $lesson = Lesson::find($lesson["lesson_id"]);

            if($lesson){
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
        })->filter();
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
        // array should look like this:
        /* 
        [
            {
                "id": 1,
                "title": "Lesson",
                "description": "Lesson",
                "thumbnail" => "lesson.jpg",
                "published_date": "2020-01-01",
                "tutor" => "Prince Chijioke",
                "views" => 1203,
                "taskSubmissions" => 504
            }
        ]
        */

        $lessons = getAuthenticatedUser()->lessons();
        $lessons->load('task');

        $myLessons = collect($lessons)->sortBy('updated_at');
        
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use App\Mail\TrackChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class TrackChangeController extends Controller
{
    public function update(Request $request){
        // validate request
        $request->validate([
            "email" => "required|email",
            "trackTitle" => "required|string",
        ]);

        // pull student details
        $student = User::whereEmail($request->email)->first();
        
        if(!$student){
            return response()->json([
                "status" => "error",
                "message" => "Student not found",
            ], 404);
        }

        
        // lead relationships
        $student->load(["submissions", "schedule", "attendance", "settings", "point", "curriculum", "progress"]);
        
        
        // pull new track details
        $track = Course::whereTitle($request->trackTitle)->first();

        if(!$track){
            return response()->json([
                "status" => "error",
                "message" => "Track not found",
            ], 404);
        }

        // pull old track details
        $oldTrack = $student->course;

        if($oldTrack->title === $track->title){
            return response()->json([
                "status" => "error",
                "message" => "Your old track and new track are the same",
            ], 400);
        }

        
        // create an array of old track details
            $oldTrackDetails = [
                "user" => [
                    "name" => $student->name,
                    "email" => $student->email,
                    "password" => $student->password,
                    "gender" => $student->gender,
                    "cv_details" => $student->cv_details,
                    "github_link" => $student->github_link,
                    "access_to_laptop" => $student->access_to_laptop,
                    "phonenumber" => $student->phonenumber,
                ],
                "submissions" => [
                    "tasks" => json_encode([]),
                ],
                "schedule" => [
                    "meetings" => json_encode([]),
                ],
                "attendance" => [
                    "record" => $student->attendance->record,
                ],
                "settings" => [
                    "notification_preference" => $student->settings->notification_preference,
                    "text_message_preference" => $student->settings->text_message_preference,
                ],
                "point" => [
                    "total" => $student->point->total,
                    "attendance_points" => $student->point->attendance_points,
                    "bonus_points" => $student->point->bonus_points,
                    "task_points" => $student->point->task_points,
                    "history" => $student->point->history,
                ],
                "curriculum" => [
                    "viewables" => $student->curriculum->viewables,
                ],
                "progress" => [
                    "course" => $track->title,
                    "course_progress" => $student->progress->course_progress,
                ],
            ];

            DB::beginTransaction();
            try{
                // delete student records
                $student->submissions()->delete();
                $student->schedule()->delete();
                $student->attendance()->delete();
                $student->point()->delete();
                $student->settings()->delete();
                $student->curriculum()->delete();
                $student->progress()->delete();
                $student->delete();
            }
            catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    "status" => "error",
                    "message" => $e->getMessage(),
                ], 400);
            }
            DB::commit();
            
            DB::beginTransaction();
            try{
            // create new record for student
            $newRecord = $track->students()->create($oldTrackDetails["user"]);
            
            // create new records for the rest of the records
            $newRecord->submissions()->create($oldTrackDetails["submissions"]);
            $newRecord->schedule()->create($oldTrackDetails["schedule"]);
            $newRecord->attendance()->create($oldTrackDetails["attendance"]);
            $newRecord->point()->create($oldTrackDetails["point"]);
            $newRecord->settings()->create($oldTrackDetails["settings"]);
            $newRecord->curriculum()->create($oldTrackDetails["curriculum"]);
            $newRecord->progress()->create($oldTrackDetails["progress"]);
            
            // send email once track change is successful
            Mail::to($newRecord->email)->queue(new TrackChanged($newRecord, $oldTrack));
            
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                "status" => "error",
                "message" => $th->getMessage(),
            ], 400);  
        }
        
        DB::commit();

        $record = User::whereEmail($request->email)->first();
        
        return response()->json([
        "status" => "success",
            "message" => "Track successfully updated",
            "data" => [
                "user" => $record,
                "track" => $record->course->track,
                ]
        ]);
            
    }
}
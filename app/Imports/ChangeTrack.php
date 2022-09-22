<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Course;
use App\Mail\TrackChanged;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ChangeTrack implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(Array $row)
    {
        $newCourse = $row["course"];
        $studentEmail = $row["email"];

        $studentDetails = [
            "user" => [
                "name" => ucfirst(strtolower($row["firstname"]))." ".ucfirst(strtolower($row["othernames"]))." ".ucfirst(strtolower($row["lastname"])),
                "avatar" => $row["avatar"],
            ]
        ];
        
        return $this->changeTrack($newCourse, $studentEmail, $studentDetails);
    }

    protected function changeTrack($newCourse, $studentEmail, Array $studentDetails){
        $course = Course::where("title", $newCourse)->first();
        
        // get new course details
        $student = User::where("email", $studentEmail)->first();
        
        // check that student account exists
        if ($student) {
            $currentCourse = $student->course;
            
            if($currentCourse->title !== $course->title){
                // save record in an array
                $studentRecord = [
                    "user" => [
                        "name" => $student->name,
                        "email" => $student->email,
                        "password" => $student->password,
                        "gender" => $student->gender,
                        "access_to_laptop" => $student->access_to_laptop,
                        "current_education_level" => $student->current_education_level,
                        "phonenumber" => $student->phonenumber,
                        "github_link" => $student->github_link,
                        "cv_details" => $student->cv_details
                    ],
                    "attendance" => [
                        "record" =>  $student->attendance->record
                    ],
                    "points" => [
                        "total" =>  $student->point->total,
                        "attendance_points" => $student->point->attendance_points,
                        "bonus_points" =>  $student->point->bonus_points,
                        "task_points" => $student->point->task_points,
                        "history" => $student->point->history
                    ],
                    "curricula" => [
                        "viewables" => $student->curriculum->viewables
                    ],
                    "schedule" => [
                        "meetings" =>  $student->schedule->meetings
                    ],
                    "settings" => [
                        "notification_preference" => $student->settings->notification_preference,
                        "text_message_preference" => $student->settings->text_message_preference
                    ],
                    "submissions" => [
                        "tasks" => $student->submissions->tasks
                    ],
                    "progress" => [
                        "course" => $course->title,
                        "course_progress" => []
                    ],
                ];
    
                $student->attendance->delete();
                $student->point->delete();
                $student->submissions->delete();
                $student->schedule->delete();
                $student->settings->delete();
                $student->curriculum->delete();
                $student->progress->delete();
                $student->delete();
    
                $newRecord = $course->students()->create([
                    "name" => $studentDetails["user"]["name"],
                    "email" =>  $studentRecord["user"]["email"],
                    "password" => $studentRecord["user"]["password"],
                    "gender" => $studentRecord["user"]["gender"],
                    "access_to_laptop" => $studentRecord["user"]["access_to_laptop"],
                    "current_education_level" => $studentRecord["user"]["current_education_level"],
                    "phonenumber" => $studentRecord["user"]["phonenumber"],
                    "github_link" => $studentRecord["user"]["github_link"],
                    "cv_details" => $studentRecord["user"]["cv_details"],
                    "avatar" => $studentDetails["user"]["avatar"],
                ]);
    
                $newRecord->attendance()->create([
                    "record" => $studentRecord["attendance"]["record"]
                ]);
    
                $newRecord->schedule()->create([
                    "meetings" => $studentRecord["schedule"]["meetings"],
                ]);
    
                $newRecord->point()->create([
                    "total" => $studentRecord["points"]["total"],
                    "attendance_points" => $studentRecord["points"]["attendance_points"],
                    "bonus_points" => $studentRecord["points"]["bonus_points"],
                    "task_points" => $studentRecord["points"]["task_points"],
                    "history" => $studentRecord["points"]["history"],
                ]);
    
                $newRecord->curriculum()->create([
                    "viewables" =>  $studentRecord["curricula"]["viewables"]
                ]);
    
                $newRecord->settings()->create([
                    "notification_preference" => $studentRecord["settings"]["notification_preference"],
                    "text_message_preference" => $studentRecord["settings"]["text_message_preference"]
                ]);
    
                $newRecord->submissions()->create([
                    "tasks" => $studentRecord["submissions"]["tasks"]
                ]);
    
                $newRecord->progress()->create([
                    "course" => $studentRecord["progress"]["course"],
                    "course_progress" => json_encode($studentRecord["progress"]["course_progress"])
                ]);
    
                Mail::to($newRecord->email)->send(new TrackChanged($newRecord, $currentCourse));
    
                return $newRecord;

            }

            $student->update([
               'avatar' => $studentDetails["user"]["avatar"] 
            ]);

            return $student;
        }

        return null;
    }
}

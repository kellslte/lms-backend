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
                "name" => ucfirst(strtolower($row["firstname"]))." ".ucfirst(strtolower($row["lastname"])),
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
                
                $student->update([
                    "avatar" => $studentDetails["user"]["avatar"],
                    "course_id" => $course->id,
                ]);
    
                $student->progress->update([
                    "course" => $course->title,
                ]);
    
                Mail::to($student->email)->send(new TrackChanged($student, $currentCourse));
    
                return $student;
            }

            $student->update([
               'avatar' => $studentDetails["user"]["avatar"] 
            ]);

            return $student;
        }

        return null;
    }
}

<?php

namespace App\Imports;

use App\Models\Course;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $course = $this->getCourseDetails($row[3]);
        
        // create the user
        $student = $course->students()->create([
            "name" => $row[0],
            "email" => $row[1],
            "gender" => $row[2],
            "phonenumber" => $row[4],
            "access_to_laptop" => $row[5],
            "current_level_of_education" => $row[7],
        ]);
        
        // create the user settings
        $student->settings()->create();

        // create the user attendance record
        $attendance = getDaysInMonth(7);
        $student->attendance()->create([
            'record' => json_encode($attendance),
        ]);

        // create the user points record
        $student->point()->create([
            'history' => json_encode([
                'user created|'
            ]),
            'bonus_points' => 10,
        ]);

        // create the user submissions record
        $student->submissions()->create([
            "tasks" => json_encode([]),
        ]);

        // create the user progress records
        $student->progress()->create([
            "course" => $course->title,
            "course_progress" => json_encode([])
        ]);

        // create user schedule
        $student->schedule()->create([
            "meetings" => json_encode([]),
        ]);

        // create user curriculum
        $student->curriculum()->create([
            "viewables" => json_encode([])
        ]);

        return $student;

    }

    protected function getCourseDetails(string $title){
        return Course::whereTitle($title)->first();
    }
}

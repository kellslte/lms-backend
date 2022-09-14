<?php
namespace App\Services;

use App\Models\Lesson;


class Curriculum {
    public static function add($user, $lesson){
        // check if the user curriculum exists
        if(!$record = $user->curriculum){
            $record = $user->curriculum->create([
                'viewables' => json_encode([])
            ]);
        }

        $createdLesson = Lesson::find($lesson);

        // update if it exists 
        $newRecord = collect(json_decode($record->viewables, true))->merge([
            "lesson_id" => $createdLesson->id,
            "lesson_status" => "uncompleted"
        ]);

        return $user->curriculum->update([
            "viewables" => json_encode($newRecord),
        ]);
    }

    public static function remove($user, $lesson){
        
    }
}
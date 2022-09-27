<?php
namespace App\Actions;

class Updatecurriculum{
    public static function execute($student, $lesson){
        $curriculum = self::getCurriculum($student);
    }

    public static function getCurriculum($student){
        return ($student->curriculum) ? $student->curriculum : $student->curriculum()->create([
            
        ]);
    }
}
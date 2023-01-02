<?php
namespace App\Services;


use App\Models\Lesson;
use App\Actions\Curriculum\UpdateCurriculum;


class Curriculum {
    public static function add($lesson){
        return UpdateCurriculum::execute($lesson);
    }

    public static function remove($user, $lesson){

    }
}

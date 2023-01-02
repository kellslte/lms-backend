<?php
namespace App\Actions\Curriculum;

class GetCurriculum {
    public static function execute($course){
        return collect(json_decode($course->curriculum->plan, true))->sortByDesc(fn($item) => $item["upload_date"]);
    }
}

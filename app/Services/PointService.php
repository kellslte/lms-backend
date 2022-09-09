<?php
namespace App\Services;

class PointService{
    public static function awardPoints($user, Array $data){

        $points = $user->point;

        $point = $points[$data["key"]];

        return $points->update([
            $data["key"] => $data["points"] + $point
        ]);
    }

    public static function penalize($user, Array $data){
        $points = $user->point;

        $point = $points[$data["key"]];

        return $points->update([
            $data["key"] => $point - $data["points"]
        ]);
    }
}
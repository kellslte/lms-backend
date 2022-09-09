<?php
namespace App\Services;

class PointService{
    public static function awardPoints($user, Array $data){
        $points = $user->point;

        return $points->update([
            $data["key"] => $data["value"]
        ]);
    }

    public static function penalize($user, Array $data){
        $points = $user->point;

        return $points->update([
            $data["key"] => $data["value"]
        ]);
    }
}
<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PointService;
use App\Http\Controllers\Controller;

class PointController extends Controller
{
    public function awardPoints(Request $request, $user){
        $data = $request->validate([
            "points" => "required|numeric" 
        ]);

        $course = getAuthenticatedUser()->course;
        $course->load('students');

        $user = User::find($user);

        $data["key"] = "bonus_points";

        if($course->students->contains($user)){
            if (!$response = PointService::awardPoints($user, $data)) {
                return response()->json([
                    "status" => "error",
                    "message" => "Points could not be awarded to this user",
                ]);
            }

            return response()->json([
                "status" => "success",
                "message" => "Points were successfully awarded to this user",
            ]);
        }

        return response()->json([
            "status" => "error",
            "message" => "You are not authorized to award points to this user"
        ], 401);
    }

    public function penalize(Request $request){}
}

<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;

class StudentPerformanceController extends Controller
{
    public function index(){
        $board = LeaderboardService::getTrackBoard(getAuthenticatedUser());

        return response()->json([
            'status' => 'success',
            'data' => [
                'board' => $board
            ]
        ], 200);
    }

    public function addBonusPointToStudent(User $user, Request $request){
        $request->validate([
            'points' => 'required|numeric',
        ]);

        // get user points
        $boardDetails = $user->points;

        // update points
        $boardDetails->update([
            'bonus_points' => (int)$request->points
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'You have successfully added points to the student'
        ], 204);
    }
}

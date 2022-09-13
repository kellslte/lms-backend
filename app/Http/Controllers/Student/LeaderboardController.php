<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    public function __invoke(){
        $board = LeaderboardService::getTrackBoard(getAuthenticatedUser());

        return response()->json([
            'status' => 'Successfull',
            "data" => [
                "leaderboard" => $board
            ]
        ]);
    }
}

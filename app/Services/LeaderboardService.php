<?php 
namespace App\Services;

use App\Models\Submission;
use App\Models\User;

class LeaderboardService {
    public static function getBoard(){
        $users = User::all()->load('submissions');

        $board = collect($users)->map(function(User $user){
            return [$user->name => collect($user->submissions)->map(function (Submission $submission) {
                    return $submission->grade;
                })->sum() / $user->submissions->count()
            ];
        });

        return $board;
    }
}
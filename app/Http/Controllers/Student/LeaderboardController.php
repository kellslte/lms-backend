<?php

namespace App\Http\Controllers\Student;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderboardController extends Controller
{
    public function __invoke(){
        $users = User::all();

        // TODO get points and arrange the points in descending order;
        $board = collect($users)->map(function ($user) {
            return [$user->name] = [
                "name" => $user->name,
                "attendances" => $user->point->attendance_points,
                "bonus" => $user->point->bonus_points,
                "task" => $user->point->task_points,
                "total" => $user->point->total,
            ];

        })->keyBy('total')->sortKeysDesc();

        return response()->json($board);
        
        array_walk($board, function($item, $index){
            return [ordinal($index++) => $item];
        });

        $user = getAuthenticatedUser();

        $userPosition = ($board->contains($user->name)) ? array_search($user->name, $board->whereStrict('name', '===', $user->name)->toArray()) : null;

        return response()->json([
            'status' => 'Successfull',
            "data" => [
                "ranking" => $board,
                "position" => $userPosition,
            ]
        ]);
    }
}

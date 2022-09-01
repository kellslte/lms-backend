<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __invoke()
    {
        $user = getAuthenticatedUser();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                "schedule" => json_decode($user->schedule->meetings, true)
            ]
        ]);
    }
}

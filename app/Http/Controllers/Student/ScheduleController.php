<?php

namespace App\Http\Controllers\Student;

use Carbon\Carbon;
use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Services\ScheduleService;
use App\Http\Controllers\Controller;

class ScheduleController extends Controller
{
    public function __invoke()
    {
        $user = getAuthenticatedUser();
        
        $schedule = [];
        
        try{
            $schedule = ScheduleService::getSchedule($user);
        }
        catch (\Exception $e) {
            $schedule["happening_today"] = [];
            $schedule["happening_this_week"] = [];
            $schedule["happening_this_month"] = [];
            $schedule["sotu"] = [];
        }
        
        return response()->json([
            'status' => 'success',
            'data' => [
                "schedule" => $schedule
            ]
        ]);
    }
}

<?php
namespace App\Services;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class AttendanceService {

    public static function mark($user){
        $records = collect(json_decode($user->attendance->record, true));

        $month = today()->format('M')."/".today()->format('Y');

        $date = today()->format('j')."/".$month;
        
        $records[] = [
            "day" => $date,
            "present" => true,
        ];

        return $user->attendance->update([ 
            "record" => json_encode($records),
        ]);
    }

    public static function getRecord($user){
        return json_decode($user->attendance->record, true);
    }
}
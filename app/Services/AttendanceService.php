<?php
namespace App\Services;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class AttendanceService {

    public static function mark(Array $data, $user){
        $record = json_decode($user->attendance->record, true);

        $dateAttended = [
            "meetingId" => $data["meetingId"],
            "date" => $data["date"],
            "present" => true,
        ];

        array_push($record, $dateAttended);

        return $user->attendance->update([
            "record" => $record,
        ]);
    }

    public static function getRecord($user){
        return json_decode($user->attendance->record, true);
    }
}
<?php
namespace App\Services;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class AttendanceService {

    public function mark($date){
        $user = getAuthenticatedUser();

        $record = json_decode($user->attendance->record, true);

        $dateAttended = [
            "date" => formatDate($date),
            "present" => true,
        ];

        array_push($record, $dateAttended);

        $user->attendance->update([
            "record" => $record,
        ]);

        return $user;
    }

    public function createRecord(){
        $user = getAuthenticatedUser();

        $data = [
            [
                "date" => formatDate(today()),
                "present" => true,
            ],
        ];

        $user->attendance()->create([
            "record" => json_encode($data),
        ]);

        return $user;
    }
}
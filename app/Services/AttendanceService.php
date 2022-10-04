<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class AttendanceService
{
    public static function mark($user)
    {
        $records = collect(json_decode($user->attendance->record, true));

        $month = today()->format('M') . "_" . today()->format('Y');

        $date = ordinal(today()->format('j')) . " " . $month;

        $newRecord = $records->map(function($record) use ($date){
            if($record["day"] === $date){
                $record["present"] = true;
            }
            return $record;
        });

        if ($records !== $newRecord) $user->attendance->update([
            "record" => json_encode($newRecord),
        ]);

        return $newRecord;
    }

    public static function getRecord($user)
    {
        $records = collect(json_decode($user->attendance->record, true))->toArray();

        return [...$records];
    }
}

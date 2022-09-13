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

        $record = $records->where("day", $date)->first();

        if($record){
            if ($record["present"] !== true) {
                $newRecord = $records->reject(function ($oldrecord) use ($record) {
                    return $oldrecord['day'] == $record['day'];
                });

                $record["present"] = true;

                $newRecord[] = $record;

                $user->attendance->update([
                    "record" => json_encode($newRecord),
                ]);
            }
        }

        return $records;
    }

    public static function getRecord($user)
    {
        return collect(json_decode($user->attendance->record, true))->toArray();
    }
}

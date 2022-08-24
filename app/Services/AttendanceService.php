<?php
namespace App\Services;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class AttendanceService {
    
    public function attend(Model $user, Model $meeting){
        $records = collect(json_decode($user->attendance->record));
        
        $record = $records->first($meeting->id);

        $newRecord = ($record) ? $record['present'] = true : $record['present'] = false;

        

    }

    public function mark(Model $model){
        $this->attendance->attendable()->where('attendable_id', $model->id)->update([
            'attended' => true,
        ]);
    }
}
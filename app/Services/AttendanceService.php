<?php
namespace App\Services;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class AttendanceService {
    protected $attendance;
    
    public function __construct(){
        $this->attendance = new Attendance();
    }
    
    public function attend(Model $model){
        $this->attendance->attendable()->associate($model);

        return $this;
    }

    public function attender(Model $model){
        $this->attendance->attender()->associate($model);

        return $this;
    }

    public function setDate($date){
        $this->attendance->date = $date;
        $this->attendance->save();
    }

    public function mark(Model $model){
        $this->attendance->attendable()->where('attendable_id', $model->id)->update([
            'attended' => true,
        ]);
    }
}
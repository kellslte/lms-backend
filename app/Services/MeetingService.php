<?php
namespace App\Services;

use App\Models\Meeting;

class MeetingService {
    protected static $meeting;

    protected $randomEmail = 'ada-events-service@ada-lms-359115.iam.gserviceaccount.com';

    public static function createMeeting($details){
        return self::$meeting = Meeting::create($details);
    }

    public static function addAttendees(){
        
    }
}
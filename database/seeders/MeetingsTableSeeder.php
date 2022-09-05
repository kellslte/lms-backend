<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\Meeting;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MeetingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = "";

        for($i = 0; $i < 20; $i++) {
            $date = Carbon::today()->addDay($i);

            $data = [
                "caption" => "General Onboarding",
                "link" => url(Str::random()),
                "start_time" => now(),
                "end_time" => now()->addMinutes(45),
            ];

            $this->createMeeting("onboarding", $data, $date);
        }
    }

    private function createMeeting($type, $data, $date){
        Meeting::create([
            ...$data,
            "host_name" => "Design Facilitator",
            "type" => $type,
            "date" => $date,
            "calendarId" => uniqid(),
        ]);
    }
}

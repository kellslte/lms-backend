<?php

namespace Database\Seeders;

use App\Models\Timeline;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimelinesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createEvent([
            "concept" => "Introductory General Knowledge",
            "start_date" => date_create("2022-09-05"),
            "end_date" => today()->addDays(2),
        ]);
        
        $this->createEvent([
            "concept" => "Track specific basics",
            "start_date" => today()->addDays(5),
            "end_date" => today()->addDays(9),
        ]);
        $this->createEvent([
            "concept" => "Track specific concepts",
            "start_date" => today()->addDays(12),
            "end_date" => today()->addDays(16),
        ]);
        $this->createEvent([
            "concept" => "Track-specific concepts + Business training events + projects",
            "start_date" => today()->addDays(19),
            "end_date" => today()->addDays(23),
        ]);
        $this->createEvent([
            "concept" => "Track-specific concepts + projects",
            "start_date" => today()->addDays(26),
            "end_date" => today()->addMonth(),
        ]);
        $this->createEvent([
            "concept" => "Track-specific concepts + projects",
            "start_date" => today()->addMonth()->addDays(2),
            "end_date" => today()->addMonth()->addDays(6),
        ]);
        $this->createEvent([
            "concept" => "Track-specific concepts + projects",
            "start_date" => today()->addMonth()->addDays(9),
            "end_date" => today()->addMonth()->addDays(13),
        ]);
        $this->createEvent([
            "concept" => " Softskills training event + examination & promotion + feedbacks",
            "start_date" => today()->addMonth()->addDays(15),
            "end_date" => today()->addMonth()->addDays(19),
        ]);
        $this->createEvent([
            "concept" => "design sprint + prep for the first collaboration",
            "start_date" => today()->addMonth()->addDays(22),
            "end_date" => today()->addMonth()->addDays(26),
        ]);
        $this->createEvent([
            "concept" => "First Collaborative projects across tracks",
            "start_date" => today()->addMonth()->addDays(59),
            "end_date" => today()->addMonths(2)->addDays(3),
        ]);
        $this->createEvent([
            "concept" => "Project demo + 2nd business training event + promotions",
            "start_date" => today()->addMonths(2)->addDays(6),
            "end_date" => today()->addMonths(2)->addDays(10),
        ]);
        $this->createEvent([
            "concept" => "deep-dive track concepts",
            "start_date" => today()->addMonths(2)->addDays(13),
            "end_date" => today()->addMonths(2)->addDays(17),
        ]);
        $this->createEvent([
            "concept" => "deep-dive + projects",
            "start_date" => today()->addMonths(2)->addDays(21),
            "end_date" => today()->addMonths(2)->addDays(25),
        ]);
        $this->createEvent([
            "concept" => "catch up week + project demos & reviews",
            "start_date" => today()->addMonths(2)->addDays(28),
            "end_date" => today()->addMonths(3)->addDays(2),
        ]);
        $this->createEvent([
            "concept" => "2nd softskills training event",
            "start_date" => today()->addMonths(3)->addDays(5),
            "end_date" => today()->addMonths(3)->addDays(9),
        ]);
        $this->createEvent([
            "concept" => "deep-dive + projects",
            "start_date" => today()->addMonths(3)->addDays(12),
            "end_date" => today()->addMonths(3)->addDays(16),
        ]);
        $this->createEvent([
            "concept" => "career training event  + connect with experts",
            "start_date" => today()->addMonths(3)->addDays(19),
            "end_date" => today()->addMonths(3)->addDays(23),
        ]);
        $this->createEvent([
            "concept" => "deep-dive track concepts",
            "start_date" => today()->addMonths(3)->addDays(26),
            "end_date" => today()->addMonths(4),
        ]);
        $this->createEvent([
            "concept" => "break + catch up",
            "start_date" => today()->addMonths(4)->addDays(3),
            "end_date" => today()->addMonths(4)->addWeek(),
        ]);
        $this->createEvent([
            "concept" => "break + catch up",
            "start_date" => today()->addMonths(4)->addDays(10),
            "end_date" => today()->addMonths(4)->addWeeks(2),
        ]);
        $this->createEvent([
            "concept" => "recaps",
            "start_date" => today()->addMonths(4)->addDays(17),
            "end_date" => today()->addMonths(4)->addWeeks(3),
        ]);
        $this->createEvent([
            "concept" => "Personal development & branding training event + examination & promotion + feedbacks",
            "start_date" => today()->addMonths(4)->addWeeks(3)->addDays(3),
            "end_date" => today()->addMonths(5)->addDay(),
        ]);
        $this->createEvent([
            "concept" => "2nd Collaborative project sprint",
            "start_date" => today()->addMonths(5)->addDays(3),
            "end_date" => today()->addMonths(5)->addWeek(),
        ]);
        $this->createEvent([
            "concept" => "track concepts + 2nd Collaborative project sprint",
            "start_date" =>
            today()->addMonths(4)->addWeeks(5)->addWeek()->addDays(3),
            "end_date" => today()->addMonths(5)->addWeek()->addDays(4),
        ]);
        $this->createEvent([
            "concept" => "project demos & reviews + promotions",
            "start_date" => today()->addMonths(5)->addWeeks(2),
            "end_date" => today()->addMonths(5)->addWeeks(2)->addDay(4),
        ]);
        $this->createEvent([
            "concept" => "Hackaton",
            "start_date" => today()->addMonths(5)->addWeeks(3),
            "end_date" => today()->addMonths(5)->addWeeks(3)->addDays(4),
        ]);
        $this->createEvent([
            "concept" => "3rd Collaborative project + track concepts",
            "start_date" => today()->addMonths(6),
            "end_date" => today()->addMonths(6)->addDays(4),
        ]);
        $this->createEvent([
            "concept" => "3rd Collaborative project + track concepts",
            "start_date" => today()->addMonths(6)->addWeek(),
            "end_date" => today()->addMonths(6)->addWeek()->addDays(4),
        ]);
        $this->createEvent([
            "concept" => "internship career fair + 3rd Collaborative projects",
            "start_date" => today()->addMonths(6)->addWeeks(2),
            "end_date" => today()->addMonths(6)->addWeeks(2)->addDays(4),
        ]);
        $this->createEvent([
            "concept" => "3rd Collaborative projects final review",
            "start_date" => today()->addMonths(6)->addWeeks(3),
            "end_date" => today()->addMonths(6)->addWeeks(3)->addDays(4),
        ]);
        $this->createEvent([
            "concept" => "3rd Collaborative projects demo ",
            "start_date" => today()->addMonths(7),
            "end_date" => today()->addMonths(7)->addDays(4),
        ]);
    }

    private function createEvent(Array $data){
        Timeline::create([
            "title" => $data["concept"],
            "start_date" => $data["start_date"],
            "end_date" => $data["end_date"],
        ]);
    }
}

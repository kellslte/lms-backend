<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TrackTableSeeder::class);
        $this->call(CourseTableSeeder::class);
        // $this->call(MeetingsTableSeeder::class);
        // $this->call(KnowledgebaseTableSeeder::class);
       $this->call(TimelinesTableSeeder::class);
        $this->call(TestUserTableSeeder::class);
    }
}

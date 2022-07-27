<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
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
        $this->createUser();
    }

    public function createUser()
    {
        User::create([
            'name' => 'Favour Max-Oti',
            'email' => 'maxotif@gmail.com',
            'gender' => 'male',
            'track_id' => 1,
            'course_id' => 2,
        ]);
    }
}

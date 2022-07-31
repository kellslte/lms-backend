<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Facilitator;
use App\Models\HelpDeskUser;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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
        $this->createTestUsers();
    }

    public function createTestUsers()
    {
        Admin::create([
            'name' => 'Favour Max-Oti',
            'email' => 'maxotif@gmail.com',
            'password' => bcrypt('password'),
        ]);

        Mentor::create([
            'name' => 'Chidera Max-Oti',
            'email' => 'deramaxoti@gmail.com',
            'password' => bcrypt('mental'),
        ]);

        Facilitator::create([
            'name' => 'Signs Madueke',
            'email' => 'signsmadueke@gmail.com',
            'password' => bcrypt('fascille'),
        ]);
        
        HelpDeskUser::create([
            'name' => 'Lilivet Ujagbor',
            'email' => 'lilivet@gmail.com',
            'password' => bcrypt('secret'),
        ]);

        $course = Course::whereTitle('Cloud Engineering')->firstOrFail();

        $course->students()->create([
            'name' => 'Amarachi Nwankwo',
            'email' => 'amaramnwankwo@gmail.com',
            'gender' => 'female',
            'phonenumber' => '08106243946'
        ]);
    }
}

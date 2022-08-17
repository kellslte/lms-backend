<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'name' => 'Favour Max-Oti',
            'email' => 'maxotif.admin@gmail.com',
            'recovery_email' => 'maxotif@gmail.com',
            'password' => bcrypt('password'),
        ])->settings()->create();

        Mentor::create([
            'name' => 'Chidera Max-Oti',
            'email' => 'deramaxoti.mentor@gmail.com',
            'recovery_email' => 'deramaxoti@gmail.com',
            'password' => bcrypt('mental'),
        ])->settings()->create();

        $course = Course::whereTitle('Cloud Engineering')->firstOrFail();

        $course->facilitator()->create([
            'name' => 'Signs Madueke',
            'email' => 'signs.facilitator@gmail.com',
            'recovery_email' => 'signsmaduaeke@gmail.com',
            'password' => bcrypt('fascille'),
        ])->settings()->create();


        $student =  $course->students()->create([
            'name' => 'Amarachi Nwankwo',
            'email' => 'amaramnwankwo@gmail.com',
            'gender' => 'female',
            'phonenumber' => '08106243946'
        ]);

        $student->settings()->create();
        $student->point()->create([
            'history' => json_encode([
                'user created ='
            ]),
            'points' => 100,
        ]);
    }
}

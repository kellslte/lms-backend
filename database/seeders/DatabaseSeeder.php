<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
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
        $this->createUser();
        $this->createRoles();
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

    public function createRoles(){
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'facilitator']);
        Role::create(['name' => 'mentor']);
        Role::create(['name' => 'student']);
    }
}

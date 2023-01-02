<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Meeting;
use Illuminate\Database\Seeder;
use App\Models\CommunityManager;
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
        // Admin::create([
        //     'name' => 'Favour Max-Oti',
        //     'email' => 'maxotif.admin@gmail.com',
        //     'recovery_email' => 'maxotif@gmail.com',
        //     'password' => bcrypt('password'),
        // ])->settings()->create();

        // Admin::create([
        //     'name' => 'Sophia Abubaka',
        //     'email' => 'sophia.admin@gmail.com',
        //     'recovery_email' => 'sophia.ahuoyiza@gmail.com',
        //     'password' => bcrypt('password'),
        // ])->settings()->create();

        // Admin::create([
        //     'name' => 'Ihuoma Agbaru',
        //     'email' => 'ihuoma.admin@gmail.com',
        //     'recovery_email' => 'theadaproject@enugutechhub.en.gov.ng',
        //     'password' => bcrypt('password'),
        // ])->settings()->create();

        // CommunityManager::create([
        //     'name' => 'Bell Omoboye',
        //     'avatar' => 'https://www.forbes.com/sites/angelicamarideoliveira/2021/07/06/meet-the-entrepreneurs-ushering-black-women-into-ux-careers-in-brazil/'
        // ])->socials()->create([
        //     'linkedin' => 'https://linkedin.com/in/bellomoboye',
        //     'twitter' => 'https://twitter.com/omobells',
        //     'facebook' => 'https://facebook.com/omoboyebell',
        //     'mail' => 'bellomoboye@gmail.com'
        // ]);

        // $this->createMentors();

        $this->createFacilitators();

        // $this->call(LessonsTableSeeder::class);

        $this->createStudents();
    }

    protected function createStudent(array $data, String $courseTitle)
    {
        $course = Course::whereTitle($courseTitle)->firstOrFail();

        $student = $course->students()->create($data);

        $student->settings()->create();

        $student->point()->create([
            'bonus_points' => 10,
        ]);

        $student->schedule()->create([
            "meetings" => json_encode([]),
        ]);

        $student->submissions()->create([
            "tasks" => json_encode([]),
        ]);

        $student->progress()->create([
            "course" => $course->title,
            "course_progress" => json_encode([]),
        ]);

        $student->curriculum()->create([
            "viewables" => json_encode([]),
        ]);

        $record = getDaysInMonth(7);

        $student->attendance()->create([
            "record" => json_encode($record),
        ]);
    }

    protected function createFacilitators()
    {
        $courses = Course::count();

        foreach (Course::all() as $course) {
            $this->createFacilitator([
                "name" => "Chidubem Anowor",
                "email" => "acount{$courses}.facilitator@gmail.com",
                "recovery_email" => "anowor{$courses}@gmail.com",
                "password" => bcrypt("password"),
            ], $course->title);

            $this->createStudent([
                "name" => "Dubem Anowor",
                "email" => "anowor{$courses}@gmail.com",
                "password" => bcrypt("password"),
            ], $course->title);

            $courses--;
        }
    }

    protected function createFacilitator(array $data, String $courseTitle)
    {
        $course = Course::whereTitle($courseTitle)->firstOrFail();

        $facilitator = $course->facilitator()->create($data);

        $facilitator->settings()->create();

        $facilitator->schedule()->create([
            "meetings" => json_encode([])
        ]);

        $facilitator->socials()->create([
            'linkedin' => 'https://linkedin.com/in/' . $data["name"],
            'twitter' => 'https://twitter.com/' . $data["name"],
            'facebook' => 'https://facebook.com/' . $data["name"],
            'mail' => $data["recovery_email"]
        ]);
    }

    protected function createStudents()
    {
    }

    protected function createMentor(array $data)
    {
        $mentor = Mentor::create([
            'name' => $data["name"],
            'email' => $data["email"],
            'recovery_email' => $data["recoveryEmail"],
            'password' => bcrypt($data["password"]),
        ]);

        $mentor->settings()->create();

        $mentor->mentees()->create([
            "mentees" => json_encode([])
        ]);
    }

    public function createMentors()
    {
        for ($i = 0; $i <= 20; $i++) {
            $this->createMentor([
                "name" => "Mentor {$i}",
                "email" => "mentor{$i}.mentor@gmail.com",
                "recoveryEmail" => "mentor{$i}@gmail.com",
                "password" => "mental"
            ]);
        }
    }
}
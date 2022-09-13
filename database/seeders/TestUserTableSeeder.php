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
        Admin::create([
            'name' => 'Favour Max-Oti',
            'email' => 'maxotif.admin@gmail.com',
            'recovery_email' => 'maxotif@gmail.com',
            'password' => bcrypt('password'),
        ])->settings()->create();
        
        Admin::create([
            'name' => 'Sophia Abubaka',
            'email' => 'sophia.admin@gmail.com',
            'recovery_email' => 'sophia.ahuoyiza@gmail.com',
            'password' => bcrypt('password'),
        ])->settings()->create();

        // CommunityManager::create([
        //     'name' => 'Bell Omoboye',
        //     'avatar' => 'https://www.forbes.com/sites/angelicamarideoliveira/2021/07/06/meet-the-entrepreneurs-ushering-black-women-into-ux-careers-in-brazil/'
        // ])->socials()->create([
        //     'linkedin' => 'https://linkedin.com/in/bellomoboye',
        //     'twitter' => 'https://twitter.com/omobells',
        //     'facebook' => 'https://facebook.com/omoboyebell',
        //     'mail' => 'bellomoboye@gmail.com'
        // ]);

        // $mentor = Mentor::create([
        //     'name' => 'Chidera Max-Oti',
        //     'email' => 'deramaxoti.mentor@gmail.com',
        //     'recovery_email' => 'deramaxoti@gmail.com',
        //     'password' => bcrypt('mental'),
        // ]);
        
        // $mentor->settings()->create();

        // $mentor->mentees()->create([
        //     "mentees" => json_encode([])
        // ]);

        // $this->createFacilitators();

        // $this->call(LessonsTableSeeder::class);

        $this->createStudents();
    }
    
    protected function createStudent(array $data, String $courseTitle)
    {
        $course = Course::whereTitle($courseTitle)->firstOrFail();

        $student = $course->students()->create($data);

        $student->settings()->create();

        $student->point()->create([
            'history' => json_encode([
                'user created|'
            ]),
            'bonus_points' => 100,
        ]);

        $student->schedule()->create([
            "meetings" => json_encode([]),
        ]);

        $lessons = [];
        $courseProgress = [];

        $student->submissions()->create([
            "tasks" => json_encode([]),
        ]);

        foreach ($course->lessons as $lesson){
            $lessons[] = [
                "lesson_id" => $lesson->id,
                "lesson_status" => "uncompleted",
            ];

            $courseProgress[] = [
                "lesson_id" => $lesson->id,
                "percentage" => 0
            ];
        }

        $student->curriculum()->create([
            "viewables" => json_encode([]),
        ]);


        $student->progress()->create([
            "course" => $course->title,
            "course_progress" => json_encode([]),
        ]);

        $record = getDaysInMonth(7);

        $student->attendance()->create([
            "record" => json_encode($record),
        ]);
    }

    protected function createFacilitators(){
       $this->createFacilitator([
            'name' => 'Signs Madueke',
            'email' => 'signs.facilitator@gmail.com',
            'recovery_email' => 'signsmaduaeke@gmail.com',
            'password' => bcrypt('fascille'),
        ], 'Cloud Engineering');

        $this->createFacilitator([
            'name' => 'Favour Onyebuchi',
            'email' => 'favour.facilitator@gmail.com',
            'recovery_email' => 'onyifavour@gmail.com',
            'password' => bcrypt('fascille'),
        ], 'Product Design');

        $this->createFacilitator([
            'name' => 'Sophia Ahuoyiza',
            'email' => 'sophia.facilitator@gmail.com',
            'recovery_email' => 'sophiaabubaka@gmail.com',
            'password' => bcrypt('fascille'),
        ], 'Backend Engineering');
    }

    protected function createFacilitator(array $data, String $courseTitle){
        $course = Course::whereTitle($courseTitle)->firstOrFail();

        $facilitator = $course->facilitator()->create($data);

        $facilitator->settings()->create();

        $facilitator->schedule()->create([
            "meetings" => json_encode([])
        ]);

        $facilitator->socials()->create([
            'linkedin' => 'https://linkedin.com/in/'.$data["name"],
            'twitter' => 'https://twitter.com/' . $data["name"],
            'facebook' => 'https://facebook.com/' . $data["name"],
            'mail' => $data["recovery_email"]
        ]);
    }

    protected function createStudents(){
        $this->createStudent([
            'name' => 'Bibi Wellington',
            'email' => 'maxotif@gmail.com',
            'current_education_level' => 'Others',
            'access_to_laptop' => 'Yes',
            'password' => bcrypt('student'),
        ], 'Product Design');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LessonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 1; $i <= 10; $i++){
            $this->makeLesson($i);
        }
    }

    protected function makeLesson($count){
        $course = Course::whereTitle('Product Design')->first();

        $lesson = $course->lessons()->create([
            'title' => 'Introduction to Design',
            'description' => 'Introduction to Design',
            'tutor' => $course->facilitator->name
        ]);
        
        $lesson->resources()->create([
            'title' => 'Design Cheatsheet',
            'resource' => 'https://drive.google.com/file/d/1bxQqth8Lph3RQLnFr-1DrjziupM0v_RD/view?usp=sharing'
        ]);

        $lesson->views()->create();

        $lesson->media()->create([
            'video_link' => 'https://www.youtube.com/watch?v=3RbpQSFdP6A',
            'thumbnail' => 'https://st2.depositphotos.com/1350793/8441/i/600/depositphotos_84416316-stock-photo-hand-pointing-to-online-course.jpg',
            'transcript' => 'https://drive.google.com/file/d/1bxQqth8Lph3RQLnFr-1DrjziupM0v_RD/view?usp=sharing',
            'youtube_video_id' => uniqid(),
        ]);

        $lesson->task()->create([
            'title' => 'And you\'re off to a start! '. $count,
            'description' => 'Do a write up explaning what you understand from the lesson you have just had',
            'task_deadline_date' => Carbon::now()->addDay(5),
            'task_deadline_time' => Carbon::now()->addHours(125),
        ]);
    }
}

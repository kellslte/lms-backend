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
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
        $this->makeLesson();
    }

    protected function makeLesson(){
        $course = Course::whereTitle('Product Design')->first();

        $lesson = $course->lessons()->create([
            'title' => 'Introduction to Design',
            'description' => 'Introduction to Design',
            'tutor' => $course->facilitator->name
        ]);
        
        $lesson->resources()->create([
            'title' => 'Design Cheatsheet',
            'resource' => 'https://www.youtube.com/redirect?event=video_description&redir_token=QUFFLUhqa2tvZkpjM0Jla0lublo3dm9mRE5Tc3MwTXYwZ3xBQ3Jtc0tsQmphMEQ5aERGb2lvUWhuOHo4THhGcnBSLVBJaXZvT0s1WkZ1aGs3c2RSZGJhQ2FQQThWSVg0c3ZSU1BnN0p3X1dLcEtPUXVHRzRsWERDLWhrVW4yb0xjT0xTVFpYVDFKSHlRcURHaGRsZHVheTA2MA&q=https%3A%2F%2Fwww.skillshare.com%2Fprofile%2FShawn-Barry%2F1750071&v=mHb2-3dsuic'
        ]);

        $lesson->views()->create([
            "views" => json_encode([])
        ]);

        $lesson->media()->create([
            'video_link' => 'https://www.youtube.com/watch?v=3RbpQSFdP6A',
            'thumbnail' => 'https://i.pinimg.com/736x/77/d8/cd/77d8cd166b2d9ba801211087f765c25d.jpg',
            'transcript' => 'https://drive.google.com/file/d/1bxQqth8Lph3RQLnFr-1DrjziupM0v_RD/view?usp=sharing',
            'youtube_video_id' => uniqid(),
        ]);

        $lesson->task()->create([
            'title' => 'And you\'re off to a start!',
            'description' => 'Do a write up explaning what you understand from the lesson you have just had',
            'task_deadline_date' => Carbon::now()->addDay(5),
            'task_deadline_time' => Carbon::now()->addHours(125),
        ]);
    }
}

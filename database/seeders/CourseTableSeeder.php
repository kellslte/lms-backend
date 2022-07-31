<?php

namespace Database\Seeders;

use App\Models\Track;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Explorer Tracks
        $this->createCourse([
            'title' =>
            'Frontend Engineering',
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Backend Engineering',
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Android Application Development',
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'iOS Application Development',
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Flutter Application Development',
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Product Development',
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Product Management',
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Data Science',
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Data Analysis',
        ], 'Explorer');

        // Innovator Tracks
        $this->createCourse([
            'title' => 'Cloud Engineering',
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Mixed Reality',
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Software Engineering (Java)',
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Augmented Reality',
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Blockchain',
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Software Engineering (C++)',
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Admin',
        ], 'Innovator');
        
        $this->createCourse([
            'title' => 'Admin',
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Admin',
        ], 'Innovator');
    }

    protected function createCourse(array $course, $track)
    {
        $courseTrack = Track::whereTitle($track)->firstOrFail();

        $course = $courseTrack->courses()->create($course);
    }
}

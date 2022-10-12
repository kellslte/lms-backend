<?php

namespace Database\Seeders;

use App\Models\Track;
use Illuminate\Database\Seeder;
use App\Services\YoutubeService;
use Illuminate\Support\Facades\Http;
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
            'playlistId' => 'PL6IFoRm0_cOalBp7n8e7PzLxPJ2bYOjeb'
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Backend Engineering',
            'playlistId' => 'PL6IFoRm0_cOaNwxp1k-nqXAzfHDpUZCsY'
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Mobile Application Development',
            'playlistId' => 'PL6IFoRm0_cOaYoaO1f0EGNCrYvrujOeZN'
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Product Design',
            'playlistId' => 'PL6IFoRm0_cOYUB-Z9wuq8W75k7Bcz8Ewc'
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Product Management',
            'playlistId' => 'PL6IFoRm0_cOaRvmhHrZtcLgM8y2QDjhc1'
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Data',
            'playlistId' => 'PL6IFoRm0_cOZ1y20wB99gql-hLfJUnegb'
        ], 'Explorer');

        // Innovator Tracks
        $this->createCourse([
            'title' => 'Cloud Engineering',
            'playlistId' => 'PL6IFoRm0_cObAuWfCbqtHOExXy34BuJch'
        ], 'Innovator');
    }

    protected function createCourse(array $course, $track)
    {
        $courseTrack = Track::whereTitle($track)->firstOrFail();

        $course = $courseTrack->courses()->create($course);
    }
}

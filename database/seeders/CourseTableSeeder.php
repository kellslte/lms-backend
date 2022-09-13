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
            'Android Application Development',
            'playlistId' => 'PL6IFoRm0_cOYwtsyB22f23ZARF4UbG0Be'
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'iOS Application Development',
            'playlistId' => 'PL6IFoRm0_cOb3-38a8SlE1u7DOkJ6p_xU'
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Flutter Application Development',
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
            'Data Science',
            'playlistId' => 'PL6IFoRm0_cOZ1y20wB99gql-hLfJUnegb'
        ], 'Explorer');

        $this->createCourse([
            'title' =>
            'Data Analysis',
            'playlistId' => 'PL6IFoRm0_cOZyYcRR1AB_JrHxaGq-GS_2'
        ], 'Explorer');

        // Innovator Tracks
        $this->createCourse([
            'title' => 'Cloud Engineering',
            'playlistId' => 'PL6IFoRm0_cObAuWfCbqtHOExXy34BuJch'
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Mixed Reality',
            'playlistId' => 'PL6IFoRm0_cOaxP4zASxZQHKbSVaydWxD1'
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Software Engineering (Java)',
            'playlistId' => 'PL6IFoRm0_cObibS7CPmxzo93srQEu_Pa4'
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Augmented Reality',
            'playlistId' => 'PL6IFoRm0_cOYqibrxrujMyTyFNYtDpDtT'
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Blockchain',
            'playlistId' => 'PL6IFoRm0_cOaiM2E5e3ZfXowu1uo_aHBU'
        ], 'Innovator');

        $this->createCourse([
            'title' => 'Software Engineering (C++)',
            'playlistId' => 'PL6IFoRm0_cOZZJSJOKOkIJhXUM0tWsCPL'
        ], 'Innovator');
    }

    protected function createCourse(array $course, $track)
    {
        $courseTrack = Track::whereTitle($track)->firstOrFail();

        $course = $courseTrack->courses()->create($course);
    }
}

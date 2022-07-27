<?php

namespace Database\Seeders;

use App\Models\Track;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TrackTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createTrack([
            'title' => 'Explorer'
        ]);

        $this->createTrack([
            'title' => 'Innovator'
        ]);
    }

    protected function createTrack(array $track)
    {
        return Track::create($track);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KnowledgebaseResource;

class KnowledgebaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 20; $i++) { 
            $this->createKnowledgebaseResource([
                'tag' => 'students',
                'title' => 'How to navigate your LMS',
                'moderator' => 'Kosi Aghadinuno',
                'thumbnail' => 'https://thumbs.dreamstime.com/b/african-american-businesswoman-holding-digital-tablet-giving-presentation-to-business-colleagues-modern-office-216881865.jpg',
                'resourceLink' => 'https://www.youtube.com/watch?v=Hv6EMd8dlQk'
            ]);
        }

        for ($i=0; $i < 15; $i++) { 
            $this->createKnowledgebaseResource([
                'tag' => 'facilitators',
                'title' => 'How to navigate your LMS',
                'moderator' => 'Kosi Aghadinuno',
                'thumbnail' => 'https://thumbs.dreamstime.com/b/african-american-businesswoman-holding-digital-tablet-giving-presentation-to-business-colleagues-modern-office-216881865.jpg',
                'resourceLink' => 'https://www.youtube.com/watch?v=Hv6EMd8dlQk'
            ]);
        }

        for ($i=0; $i < 15; $i++) { 
            $this->createKnowledgebaseResource([
                'tag' => 'mentors',
                'title' => 'How to navigate your LMS',
                'moderator' => 'Kosi Aghadinuno',
                'thumbnail' => 'https://thumbs.dreamstime.com/b/african-american-businesswoman-holding-digital-tablet-giving-presentation-to-business-colleagues-modern-office-216881865.jpg',
                'resourceLink' => 'https://www.youtube.com/watch?v=Hv6EMd8dlQk'
            ]);
        }

        for ($i=0; $i < 15; $i++) { 
            $this->createKnowledgebaseResource([
                'tag' => 'admins',
                'title' => 'How to navigate your LMS',
                'moderator' => 'Kosi Aghadinuno',
                'thumbnail' => 'https://thumbs.dreamstime.com/b/african-american-businesswoman-holding-digital-tablet-giving-presentation-to-business-colleagues-modern-office-216881865.jpg',
                'resourceLink' => 'https://www.youtube.com/watch?v=Hv6EMd8dlQk'
            ]);
        }

    }

    private function createKnowledgebaseResource(Array $data){
        return KnowledgebaseResource::create([
            "tag" => $data['tag'],
            "title" => $data['title'],
            "moderator" => $data['moderator'],
            "resource_link" => $data["resourceLink"],
            "thumbnail" => $data['thumbnail'],
        ]);
    }
}

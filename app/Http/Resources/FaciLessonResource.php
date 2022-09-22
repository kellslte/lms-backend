<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class FaciLessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->status === "unpublished"){
            return [
                "id" => $this->id,
                "title" => $this->title,
                "description" => $this->description,
                "thumbnail" => Storage::get($this->media->thumbnail),
                "videoLink" => Storage::get($this->media->video_link),
                "resources" => $this->resources,
            ];
        }
        
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "thumbnail" => $this->media->thumbnail,
            "videoLink" => $this->media->video_link,
            "resources" => $this->resources,
            "tasks" => $this->tasks
        ];
    }
}

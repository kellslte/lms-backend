<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $percentage = collect(json_decode(getAuthenticatedUser()->progress->course_progress, true));

        $lessonPercentage = $percentage->where("lesson_id", $this->id)->first();

        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "video_link" => $this->media->video_link,
            "thumbnail" => $this->media->thumbnail,
            "transcript" => null,
            "task" => $this->task,
            "resources" => $this->resources,
            "percentage" => $lessonPercentage["percentage"]
        ];
    }
}

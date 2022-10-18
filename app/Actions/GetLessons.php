<?php

namespace App\Actions;

use App\Models\Facilitator;
use App\Services\TaskManager;
use Illuminate\Support\Collection;

class GetLessons
{
    public static function handle(Collection $published, Collection $unpublished, Facilitator $user): array
    {
                $publishedLessons = $published->map(fn($lesson) => [
                        "id" => $lesson->id,
                        "status" => $lesson->status,
                        "thumbnail" => $lesson->media->thumbnail ?? null,
                        "title" => $lesson->title,
                        "description" => $lesson->description,
                        "datePublished" => formatDate($lesson->created_at),
                        "tutor" => $lesson->tutor,
                        "views" => 0,
                        "taskSubmissions" => count(TaskManager::getSubmissions($lesson->tasks, $user->course->students))
                    ]);

                $unpublishedLessons = $unpublished->map(fn($lesson) => [
                        "id" => $lesson->id,
                        "status" => $lesson->status,
                        "thumbnail" => $lesson->media->thumbnail ?? null,
                        "title" => $lesson->title,
                        "description" => $lesson->description,
                        "datePublished" => formatDate($lesson->created_at),
                        "tutor" => $user->name,
                        "views" => 0,
                        "taskSubmissions" => count(TaskManager::getSubmissions($lesson->tasks, $user->course->students))
                ]);


                return [
                    "published_lessons" => [...$publishedLessons],
                    "unpublished_lessons" => [...$unpublishedLessons],
                ];
    }
}

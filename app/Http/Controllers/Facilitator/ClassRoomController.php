<?php

namespace App\Http\Controllers\Facilitator;

use Illuminate\Http\Request;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLessonRequest;

class ClassRoomController extends Controller
{
    public function __construct(Public $lessonService){
        $this->lessonService = new LessonsService(getAuthenticatedUser());
    }

    public function index(String $query = 'all'){
        if($query !== 'all') return ($query === 'published') ? $this->lessonService->getPublishedLessons() : $this->lessonService->getUnpublishedLessons();

        return $this->lessonService->getAllLessons();
    }

    public function store(CreateLessonRequest $request){
        return $this->lessonsService->createLesson($request);
    }
}

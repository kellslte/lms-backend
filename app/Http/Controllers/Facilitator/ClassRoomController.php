<?php

namespace App\Http\Controllers\Facilitator;

use Illuminate\Http\Request;
use App\Services\LessonsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLessonRequest;

class ClassRoomController extends Controller
{
    public function index(){
       $user = getAuthenticatedUser();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'lessons' => LessonsService::getAllLessons($user),
            ]
        ], 200);
    }

    public function store(CreateLessonRequest $request){
        $user = getAuthenticatedUser();

        
    }
}

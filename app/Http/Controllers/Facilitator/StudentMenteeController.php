<?php

namespace App\Http\Controllers\Facilitator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mentor;
use App\Models\Mentees;

class StudentMenteeController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();

       $mentors = $user->course->mentors;

       $mentors->load('mentees');

        return response()->json([
            'status' => 'success',
            'data' => [
                'mentees' => $mentors->mentees
            ]
        ]);
    }

    public function assignMenteeToMentor(Mentor $mentor, ){
        $user = getAuthenticatedUser();

        if($mentor->mentees <= 5){
            $mentor->mentees()->create();
        }
    }

    public function removeMentee(){}
}

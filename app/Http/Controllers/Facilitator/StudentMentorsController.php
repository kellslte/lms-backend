<?php

namespace App\Http\Controllers\Facilitator;

use App\Models\User;
use App\Models\Mentor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mentees;

class StudentMentorsController extends Controller
{
    public function index()
    {
        $user = getAuthenticatedUser();

        $students = $user->course->students;

        $mentors  = $user->course->mentors;

        $data = collect($mentors)->map(function($mentor){
            return [
                $mentor->name = collect($mentor->mentees)->map(function($mentee){
                    return $mentee;
                })
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'mentors' => $data
            ]
        ]);
    }

    public function assignMenteeToMentor(Mentor $mentor, User $user)
    {
        $students = 

        $mentor->mentees->update([
            'mentees' => json_encode([
                "studentId" => $user->id,
            ])
        ]);

        $mentees = collect($mentor->mentees)->map(function($mentee){

        });

        return response()->json([
            'status' => 'success',
            'message' => 'Mentee has been associated with mentor',
            'data' => [
                'mentor' => [$mentor->name => json_decode($mentor->mentees)]
            ]
        ]);
    }

    public function removeMenteeFromMentor(Mentor $mentor, User $user)
    {
        $mentees = $mentor->mentees;

        return response()->json($mentees);
    }
}

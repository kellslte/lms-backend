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
        $mentors  = Mentor::all();

        $data = collect($mentors)->map(function($mentor){
            return [
                "id" => $mentor->id,
                "name" => $mentor->name,
                "email" => $mentor->recovery_email,
                "mentees" => json_decode($mentor->mentees->mentees, true)
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
        $mentees = collect(json_decode($mentor->mentees->mentees, true));

        $record = $mentees->where("studentId", $user->id)->first();

        if($record){
            return response()->json([
                "status" => "failed",
                "message" => "This student has already been assigned to this mentor"
            ]);
        }

        try{
            array_push($mentees->toArray(), [
                "studentId" => $user->id,
                "studentName" => $user->name
            ]);

            $mentor->mentees->update([
                'mentees' => json_encode($mentees)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Mentee has been associated with mentor',
                'data' => [
                    'mentor' => [$mentor->name => json_decode($mentor->mentees, true)]
                ]
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function removeMenteeFromMentor(Mentor $mentor, User $user)
    {
        $mentees = json_decode($mentor->mentees->mentees, true);

        try{
            $response = collect($mentees)->reject(function ($mentee) use ($user) {
                return $mentee["studentId"] === $user->id;
            })->flatten();

            $mentor->mentees->update([
                "mentees" => json_encode($response)
            ]);

            return response()->json([
                "status" => "successful",
                "message" => "Student record has been successfully removed"
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "messsage" => $e->getMessage()
            ]);
        }
    }
}

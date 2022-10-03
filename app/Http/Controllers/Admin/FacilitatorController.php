<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Mail\UserOnboarded;
use App\Models\Facilitator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CreateFacilitatorRequest;

class FacilitatorController extends Controller
{
    public function index(){
        $facilitators = Facilitator::all();

        $data = [];

        foreach ($facilitators as $facilitator) {
            $facilessons = $facilitator->course->lessons;

            $facilessons->load('tasks');

            $tasks = collect($facilessons)->map(function ($lesson){
                return $lesson->tasks->count();
            })->sum();

            $data[] = [
                'name' => $facilitator->name,
                'course' => $facilitator->course->title,
                'lessons' => $facilitator->course->lessons->count(),
                'tasks' => $tasks
            ];
        }
        
        return response()->json([
            "status" => "success",
            "data" => [
                "facidata" => $data
            ]
        ]);
    }

    public function store(CreateFacilitatorRequest $request)
    {
        $course = Course::whereTitle($request->course_title)->first();

        if (!$course) return response()->json([
            'status' => 'error',
            'message' => 'Course not found',
        ]);

        try {
            $password = generatePassword(7);

            $facilitator = $course->facilitator()->create([
                'name' => $request->name,
                'email' => $request->email,
                'recovery_email' => $request->recovery_email,
                'password' => bcrypt($password),
            ]);

            $facilitator->settings()->create();

            Mail::to($facilitator->recovery_email)->queue(new UserOnboarded($facilitator, $password));

            return response()->json([
                'status' => 'success',
                'message' => 'Facilitator account has been created and email has been sent successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Facilitator account could not be created'
            ], 400);
        }
    }

    public function update(){}

    public function sendMessage(Request $request){
        
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use App\Mail\UserOnboarded;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CreateFacilitatorRequest;

class OnboardingController extends Controller
{
    public function facilitator(CreateFacilitatorRequest $request){
        try{
            $course = Course::whereTitle($request->course_title)->first();

            if (!$course) return response()->json([
                'status' => 'error',
                'message' => 'Course not found',
            ]);

            $password = generatePassword(7);

            $facilitator = $course->facilitator()->create([
                'name' => $request->name,
                'email' => $request->email,
                'recovery_email' => $request->recovery_email,
                'password' => bcrypt($password),
            ])->settings()->create();

            // TODO send notification email to the facilitator
            Mail::to($facilitator->recovery_email)->queue(new UserOnboarded($facilitator, $password));

            // TODO return response
                return response()->json([
                    'status' => 'success',
                    'message' => 'Facilitator account has been created and '
                ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Facilitator account could not be created'
            ]);
        }
    }

    public function sendMagicLinkToStudents(){
        $users = User::all();

        foreach($users as $user){
            $user->sendMagicLink();
        }
    }
}

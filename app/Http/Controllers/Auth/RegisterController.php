<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Track;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\PaystackService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CreateUserRequest;
use App\Mail\SendWelcomeMailToNewUser;

class RegisterController extends Controller
{
    public function register(CreateUserRequest $request){
        //return response()->json($request->trackId);
        
        // create user
        $user = User::create([
            'name' => $request->fullname,
            'email' => $request->email,
            'gender' => $request->gender,
            'access_to_laptop' => $request->accessToLaptop,
            'current_education_level' => $request->currentEducationLevel,
            'phonenumber' => $request->phonenumber,
            'github_link' => $request->githubLink,
            'cv_details' => $request->cvDetails,
            'track_id' => $request->trackId,
            'course_id' => $request->courseId,
        ]);

        // assign role
        $user->assignRole('student');

        //  create transaction details and set it to unpaid
        $user->transaction()->create([
            'email' => $user->email,
            'amount' => 7500,
            'status' => 'unpaid',
        ]);

        // TODO Send Welcome Email to user
        Mail::to($user->email)->queue(new SendWelcomeMailToNewUser($user));

        // return response on account creation
        return response()->json([
            'status' => 'successful',
            'data' => [
                'user' => $user,
                'role' => $user->roles[0]->name,
            ]
        ]);
    }

    public function getTracksAndCourses(){
        $tracks = Track::all();

        $collection = collect($tracks)->map(function($track){
            return [$track->title => $track->courses];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'tracks_and_courses' => $collection,
            ]
        ]);
    }
}

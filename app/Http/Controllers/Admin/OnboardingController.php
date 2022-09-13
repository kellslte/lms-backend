<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Course;
use App\Models\Mentor;
use App\Mail\UserOnboarded;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Events\SendMagicLink;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CreateMentorRequest;
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
                'recovery_email' => $request->recoveryEmail,
                'password' => bcrypt($password),
            ]);
            
            $facilitator->settings()->create();

            $facilitator->socials()->create([
                "twitter" => $request->twitter,
                "linkedin" => $request->linkedin,
                "facebook" => $request->facebook,
                "mail" => $request->email,
            ]);

            Mail::to($facilitator->recovery_email)->queue(new UserOnboarded($facilitator, $password));

    
            return response()->json([
                'status' => 'success',
                'message' => 'Facilitator account has been created and email has been sent successfully'
            ], 201);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Facilitator account could not be created'
            ], 400);
        }
    }

    public function students(Request $request){
        $usersSheet = $request->file('users');

        return Excel::import(new UsersImport, $usersSheet);
    }

    public function sendMagicLink(string $student){
        if($student = User::find($student)){
            return $student->sendMagicLink();
        }
        
        return response()->json([
            "status" => "success",
            "message" => "Magic link has been sent successfully"
        ]);
    }

    public function sendMagicLinkToStudents(){
        $users = User::all();

       try {
            SendMagicLink::dispatch($users);

            return response()->json([
                'status' => "success",
                'data' => [
                    'message' => 'students have been onboarded and magic links have been sent to them.'
                ]
            ], 200);
       } catch (\Throwable $th) {
        return response()->json([
            'status' => 'failed',
            'data' => [
                'message' => 'magic links could not be sen to the students, please contact your administrator.',
            ]
        ], 400);
       }
    }

    public function mentor(CreateMentorRequest $request){
        try{
            $password = generatePassword(7);

            $mentor = Mentor::create([
                'name' => $request->name,
                "email" => $request->email,
                'recovery_email' => $request->recoveryEmail,
                'password' => $password,
            ]);
            
            $mentor->settings()->ceate();

            $mentor->socials()->create([
                "twitter" => $request->twitter,
                "linkedin" => $request->linkedin,
                "facebook" => $request->facebook,
                "mail" => $request->email,
            ]);

            $mentor->mentees()->create([
                "mentees" => json_encode([])
            ]);

            Mail::to($mentor->recovery_email)->queue(new UserOnboarded($mentor, $password));

            return response()->json([
                'status' => 'success',
                'message' => 'Mentor account has been created and email has been sent successfully'
            ], 201);

        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor account could not be created'
            ], 400);
        }
    }
}

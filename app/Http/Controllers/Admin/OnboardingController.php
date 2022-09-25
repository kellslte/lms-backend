<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Mentor;
use App\Mail\TrackChanged;
use App\Mail\UserOnboarded;
use App\Imports\ChangeTrack;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Events\SendMagicLink;
use App\Mail\SlackInviteMail;
use App\Events\SendSlackInvite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CreateMentorRequest;
use App\Http\Requests\CreateFacilitatorRequest;

class OnboardingController extends Controller
{
    public function facilitator(CreateFacilitatorRequest $request)
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

    public function students(Request $request)
    {   
        $usersSheet = $request->file('users');

        return Excel::import(new UsersImport, $usersSheet);
    }

    public function changeTrack(Request $request)
    {
        $data = $request->validate([
            "newTrack" => 'required|string',
            "email" => 'required|email',
        ]);

        $course = Course::where("title", $data["newTrack"])->first();
        
        // get new course details
        $student = User::where("email", $data["email"])->first();

        // check that student account exists
        if (!$student) {
            return response()->json([
                "status" => "error",
                "message" => "User record not found"
            ], 404);
        }
        
        try{
            $formerCourse = $student->course;
            
            if($course->title !== $formerCourse->title){

                $student->update([
                    "course_id" => $course->id,
                ]);

                $student->progress->update([
                    "course" => $course->title,
                ]);

                Mail::to($student->email)->send(new TrackChanged($student, $formerCourse));

                return response()->json([
                    "status" => "successful",
                    "message" => "Track has been succesfully changed",
                    "account" => $student,
                    "track" => $student->course->title
                ]);
            }

            return response()->json([
                "status" => "failed",
                "message" => "Your track is the same as the one you want to change to"
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function bulkChangeTrack(Request $request){
        $usersSheet = $request->file("users");

        return Excel::import(new ChangeTrack, $usersSheet);
    }

    public function sendMagicLink(Request $request)
    {
        if (!$student = User::whereEmail($request->email)->first()) {
            return response()->json([
                "status" => "failed",
                "message" => "User record could not be found"
            ], 404);
        }

        try {
            $student->sendMagicLink();

            return response()->json([
                "status" => "success",
                "message" => "Magic link has been sent successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Could not send link",
            ], 400);
        }
    }

    public function sendMagicLinkToStudents()
    {
        $users = User::all();

        try {
            SendMagicLink::dispatch($users, Admin::all());

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

    public function mentor(CreateMentorRequest $request)
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor account could not be created'
            ], 400);
        }
    }

    public function sendSlackInvite(Request $request)
    {
        $students = User::all();

        $request->validate([
            "link" => "required|string"
        ]);

        try {
            SendSlackInvite::dispatch($students, $request->link);

            return response()->json([
                "status" => "successful",
                "message" => "Slack invite mail has been sent to the students"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ], 400);
        }
    }

    public function sendStudentSlackInvite(Request $request){

        $request->validate([
            "email" => "required|email",
            "link" => "required|string"
        ]);

        $student = User::whereEmail($request->email)->first();

        if(!$student){
            return response()->json([
               'status' => 'error',
               'message' => 'The user record could not be found',
            ]);
        }

        try{
            Mail::to($student->email)->send(new SlackInviteMail($student, $request->link));

            return response()->json([
                "status" => "successful",
                'message' => 'Slack invite successfully sent'
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ]);
        }
    }
}

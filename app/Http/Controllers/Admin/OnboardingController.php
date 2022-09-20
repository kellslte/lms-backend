<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Mentor;
use App\Mail\TrackChanged;
use App\Mail\UserOnboarded;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Events\SendMagicLink;
use App\Events\SendSlackInvite;
use App\Events\CreateCurriculum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CreateMentorRequest;
use App\Http\Requests\CreateFacilitatorRequest;

class OnboardingController extends Controller
{
    public function facilitator(CreateFacilitatorRequest $request)
    {
        try {
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
            "currentTrack" => 'required|string',
            "email" => 'required|email',
        ]);

        $currentCourse = Course::where("title", $data["currentTrack"])->first();
        $course = Course::where("title", $data["newTrack"])->first();
        
        // get new course details
        $student = $currentCourse->students()->where("email", $data["email"])->first();

        // check that student account exists
        if (!$student) {
            return response()->json([
                "status" => "error",
                "message" => "User record not found"
            ], 404);
        }

        
        try{
            // save record in an array
            $studentRecord = [
                "user" => [
                    "name" => $student->name,
                    "email" => $student->email,
                    "password" => $student->password,
                    "gender" => $student->gender,
                    "access_to_laptop" => $student->access_to_laptop,
                    "current_education_level" => $student->current_education_level,
                    "phonenumber" => $student->phonenumber,
                    "github_link" => $student->github_link,
                    "cv_details" => $student->cv_details
                ],
                "attendance" => [
                  "record" =>  $student->attendance->record  
                ],
                "points" => [
                    "total" =>  $student->point->total,
                    "attendance_points" => $student->point->attendance_points,
                    "bonus_points" =>  $student->point->bonus_points,
                    "task_points" => $student->point->task_points,
                    "history" => $student->point->history
                ],
                "curricula" => [
                    "viewables" => $student->curriculum->viewables
                ],
                "schedule" => [
                    "meetings" =>  $student->schedule->meetings
                ],
                "settings" => [
                    "notification_preference" => $student->settings->notification_preference,
                    "text_message_preference" => $student->settings->text_message_preference
                ],
                "submissions" => [
                    "tasks" => $student->submissions->tasks
                ],
                "progress" => [
                    "course" => $course->title,
                    "course_progress" => []
                ],
                "notifications" => $student->notifications
            ];

            $student->attendance->delete();
            $student->point->delete();
            $student->submissions->delete();
            $student->settings->delete();
            $student->curriculum->delete();
            $student->progress->delete();
            $student->delete();

            $newRecord = $course->students()->create([
                "name" => $studentRecord["user"]["name"],
                "email" =>  $studentRecord["user"]["email"],
                "password" => $studentRecord["user"]["password"],
                "gender" => $studentRecord["user"]["gender"],
                "access_to_laptop" => $studentRecord["user"]["access_to_laptop"],
                "current_education_level" => $studentRecord["user"]["current_education_level"],
                "phonenumber" => $studentRecord["user"]["phonenumber"],
                "github_link" => $studentRecord["user"]["github_link"],
                "cv_details" => $studentRecord["user"]["cv_details"]
            ]);

            $newRecord->attendance()->create([
                "record" => $studentRecord["attendance"]["record"]
            ]);

            $newRecord->point()->create([
                "total" => $studentRecord["points"]["total"],
                "attendance_points" => $studentRecord["points"]["attendance_points"],
                "bonus_points" => $studentRecord["points"]["bonus_points"],
                "task_points" => $studentRecord["points"]["task_points"],
                "history" => $studentRecord["points"]["history"],
            ]);

            $newRecord->curriculum()->create([
                "viewables" =>  $studentRecord["curricula"]["viewables"]
            ]);

            $newRecord->settings()->create([
                "notification_preference" => $studentRecord["settings"]["notification_preference"],
                "text_message_preference" => $studentRecord["settings"]["text_message_preference"]
            ]);

            $newRecord->submissions()->create([
                "tasks" => $studentRecord["submissions"]["tasks"]
            ]);

            $newRecord->progress()->create([
                "course" => $studentRecord["progress"]["course"],
                "course_progress" => json_encode($studentRecord["progress"]["course_progress"])
            ]);

            Mail::to($newRecord->email)->send(new TrackChanged($newRecord, $currentCourse));

            return response()->json([
                "status" => "successful",
                "message" => "Track has been succesfully changed",
                "account" => $newRecord
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "failed",
                "message" => $e->getMessage()
            ]);
        }
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

    public function createCurriculum(){
        $users = User::all();

        try{
            CreateCurriculum::dispatch($users);

            return response()->json([
                "status" => "successful",
                "message" => "Curriculum created successfully",
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }
}

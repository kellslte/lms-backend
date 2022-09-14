<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
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

    public function changeTrack(Request $request){
        $data = $request->validate([
            "newTrack" => '',
            "oldTrack" => '',
            "email" => '',
        ]);

        // get course details
        $course = Course::whereTitle($data["oldTrack"])->first();

        $oldAccount = $course->students()->where("email", $data["email"])->first();

        $oldAccount->load(['attendance', 'schedule', 'point', 'submissions', 'progress', 'settings', 'curriculum']);

        $newCourse = Course::whereTitle($data["newTrack"])->first();

        $newAccount = $newCourse->students()->create([
            "name" => $oldAccount->name,
            "email" => $oldAccount->email,
            "gender" => $oldAccount->gender,
            "access_to_laptop" => $oldAccount->access_to_laptop,
            "current_education_level" => $oldAccount->current_education_level,
            "phonenumber" => $oldAccount->phonenumber,
            "github_link" => $oldAccount->github_link,
            "cv_details" => $oldAccount->cv_details,
        ]);

        $newAccount->submissions()->create([
            "tasks" => $oldAccount->submissions->tasks,
        ]);

        $newAccount->settings()->create([...$oldAccount->settings]);

        $newAccount->progress()->create([
            "course" => $newCourse->title,
            "course_progress" => $oldAccount->progress->course_progress,
        ]);

        $newAccount->schedule()->create([
            "meetings" => $oldAccount->schedule->meetings
        ]);

        // lesson videso for the curriculum
        $viewables = collect($newCourse->lessons)->map(function($lesson){
            return [
                "lesson_id" => $lesson->id,
                "lesson_status" => "uncompleted"
            ];
        });

        $newAccount->curriculum()->create([
            "viewables" => $viewables,
        ]);

        $newAccount->point()->create([...$oldAccount->points]);

        $newAccount->attendance()->create([...$oldAccount->attendance]);
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
}

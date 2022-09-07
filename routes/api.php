<?php

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\YoutubeService;

// Authentication and Authorization Controllers
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Auth\LoginController;

// Super Admin Controllers
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\KnowledgebaseController;
use App\Http\Controllers\Auth\MagicLoginController;
use App\Http\Controllers\Admin\OnboardingController;

// Student Controllers
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Facilitator\StudentMentorsController;
use App\Http\Controllers\Student\TaskController as StudentTaskController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Mentor\ProfileController as MentorProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// Facilitator Controllers
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Facilitator\TaskController as FacilitatorTaskController;
use App\Http\Controllers\Student\HelpdeskController as StudentHelpdeskController;
use App\Http\Controllers\Student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\Student\ClassroomController as StudentClassroomController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Facilitator\ProfileController as FacilitatorProfileController;
use App\Http\Controllers\Facilitator\ScheduleController as FacilitatorScheduleController;
use App\Http\Controllers\Facilitator\ClassRoomController as FacilitatorClassRoomController;
use App\Http\Controllers\Facilitator\DashboardController as FacilitatorDashboardController;
use App\Http\Controllers\Facilitator\StudentPerformanceController as FacilitatorsStudentPerformanceControler;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('videos', function (Request $request) {
    $client = new YoutubeService();

    return $client->uploadVideo($request);
});

// Route::post('transcript', function(Request $request){
//     $file = $request->file("lessonTranscript");

//     $fileName = $file->getClientOriginalName();
//     $extension = $file->getClientOriginalExtension();

//     $newfilename = $fileName. now().".".$extension;

//     $transcript = $request->file()->storeAs("/lessons/transcripts", $newfilename, "public");

//     $url = Storage::disk("local")->url($transcript);

//     return response()->json($url);
// });


Route::prefix('v1')->group(function(){
    // Magic Link Login
    Route::middleware('guest')->post('auth/magic/login/{token}', [MagicLoginController::class, 'checkUserAndRedirect'])->name('verify-login');

    Route::post('auth/magic/send-token', [MagicLoginController::class, 'sendLoginLink']);

    // Student Registration Routes
    Route::post('auth/login', [LoginController::class, 'checkUserLogin']);

    // Password Reset Routes
    Route::post('auth/password/{user}/send-reset-link', [PasswordController::class, 'checkUserIdentity']);

    Route::post('auth/password/{user}/reset', [PasswordController::class, 'checkUserIdentityForReset']);
    
    
    // Protected Routes
    Route::middleware('auth:sanctum')->group(function(){

        Route::prefix('auth')->group(function(){
            // system wide notifications
            Route::get("notifications", function() {
                return response()->json([
                    'success' => true,
                    'data' => [
                        "notifications" => getAuthenticatedUser()->notifications,
                    ]
                ]);
            });

            // knowledgebase resource
            Route::get('knowledgebase/resources', KnowledgebaseController::class);
        });


        Route::post('password/change', [PasswordController::class, 'changePassword']); 
        
        // Student Routes
        Route::prefix('auth/user')->group(function(){
            // User Logout
            Route::post('logout', fn() => (new LoginController)->logout('student'));
            // Create New User Password
            Route::post('password/create', [PasswordController::class, 'createPassword']);
            // Dashboard Route
            Route::get('dashboard', [StudentDashboardController::class, 'index']);
            // Profile Route
            Route::get('profile', [StudentProfileController::class, 'index']);
            // Change Proflie Settings Route
            Route::post('profile', [StudentProfileController::class, 'storeSettings']);
            // Leaderboard
            Route::get('leaderboard', LeaderboardController::class);
            // Schedule
            Route::get('schedule', StudentScheduleController::class);
            // Classroom Routes
            Route::get('classroom', [StudentClassroomController::class, 'index']);
            // Get Student Lessons
            Route::get('classroom/lessons', [StudentClassroomController::class, 'getStudentLessons']);
            // Get Classroom Progress
            Route::get('classroom/progress', [ProgressController::class, 'getStudentProgress']);
            // Increment lesson progress
            Route::put('classroom/progress/{lesson}', [ProgressController::class, 'incrementStudentProgress']);
            // Get Single Lesson from classroom
            Route::get('classroom/lessons/{lesson}', [StudentClassroomController::class, 'getLesson']);
            // Mark attendance for a meeting
            
            // Task Routes
            Route::get('tasks', [StudentTaskController::class, 'index']);
            // Submit Task
            Route::post('tasks/{task}', [StudentTaskController::class, 'submit']);
            // Helpdesk route
            Route::get('helpdesk', [StudentHelpdeskController::class, 'index']);
            // Report a problem
            Route::post('issues/report', [StudentHelpdeskController::class, 'report']);
        });

        // Admin Routes
        Route::prefix('auth/admin')->group(function(){
            // Admin Logout
            Route::post('logout', fn() => (new LoginController)->logout('admin'));
            // Change password Route
            Route::post('password/change', [PasswordController::class, 'changePassword']);
            // Profile Route
            Route::get('profile', [AdminProfileController::class, 'index']);
            // Change Proflie Settings Route
            Route::post('profile', [AdminProfileController::class, 'storeSettings']);

            // Onboarding Routes
            Route::post('onboard', [OnboardingController::class, 'facilitator']);

            Route::post('onboad/send-login-link', [OnboardingController::class, 'sendMagicLinkToStudents']);
        });

        // Facilitator Routes
        Route::prefix('auth/facilitator')->group(function(){
            Route::post('logout', fn() => (new LoginController)->logout('facilitator'));
            // Change Password Route
            Route::post('password/change', [PasswordController::class, 'changePassword']);
            // Profile Route
            Route::get('profile', [FacilitatorProfileController::class, 'index']);
            // Change Proflie Settings Route
            Route::post('profile', [FacilitatorProfileController::class, 'storeSettings']);
            // Class Room Routes
            Route::get('dashboard', FacilitatorDashboardController::class);
            // Classroom data route
            Route::get('classroom', [FacilitatorClassroomController::class, 'index']);
            // Create lesson route
            Route::post('classroom', [FacilitatorClassroomController::class, 'store']);
            // Get facilitator schedule
            Route::get('schedule', [FacilitatorScheduleController::class, 'index']);
            // Create a meeting or live event
            Route::post('schedule', [FacilitatorScheduleController::class, 'fixLiveClass']);
            // Get Tasks Route
            Route::get('tasks', [FacilitatorTaskController::class, 'index']);
            // Get particular task with students that have submitted theirs
            Route::get('tasks/{task}', [FacilitatorTaskController::class, 'viewSubmissions']);
            // create lesson task route
            Route::post('tasks/{lesson}', [FacilitatorTaskController::class, 'store']); 
            // update lesson task route
            Route::put('tasks/{task}', [FacilitatorTaskController::class, 'update']);
            // grade lesson task
            Route::put('tasks/{task}/grade/{user}', [FacilitatorTaskController::class, 'gradeTask']);
            // Student Leaderboard route
            Route::get('students/leaderboard', [FacilitatorsStudentPerformanceControler::class, 'index']);
            // Add bonus point to student
            Route::put('students/leaderboard/{user}/points/bonus', [FacilitatorsStudentPerformanceControler::class, 'addBonusPointToStudent']);
            // Student mentor route
            Route::get('mentors', [StudentMentorsController::class, 'index']);
            // Assign mentee to mentor
            Route::post('mentors/{mentor}/mentees/{user}', [StudentMentorsController::class, 'assignMenteeToMentor']);
            // Remove mentee from mentor
            Route::delete('mentors/{mentor}/mentees/{user}', [StudentMentorsController::class, 'removeMenteeFromMentor']);
        });

        // Mentor Routes
        Route::prefix('auth/mentor')->group(function(){
            Route::post('auth/logout', fn() => (new LoginController)->logout('mentor'));
        
            // Create Password Route
            Route::post('auth/mentor/password/create', [PasswordController::class, 'createPassword']);
            // Profile Route
            Route::get('profile', [MentorProfileController::class, 'index']);
            // Change Proflie Settings Route
            Route::post('profile', [MentorProfileController::class, 'storeSettings']);

        });
    });


});
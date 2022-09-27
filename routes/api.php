<?php

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\YoutubeService;

// Authentication and Authorization Controllers
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Super Admin Controllers
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\KnowledgebaseController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Auth\MagicLoginController;

// Student Controllers
use App\Http\Controllers\Admin\OnboardingController;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Admin\TrackChangeController;
use App\Http\Controllers\Facilitator\PointController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Facilitator\StudentMentorsController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Student\TaskController as StudentTaskController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Mentor\ProfileController as MentorProfileController;

// Facilitator Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Facilitator\TaskController as FacilitatorTaskController;
use App\Http\Controllers\Student\HelpdeskController as StudentHelpdeskController;
use App\Http\Controllers\Student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\Student\ClassroomController as StudentClassroomController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Facilitator\ProfileController as FacilitatorProfileController;

// Mentor Controllers
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
    //return (new YoutubeService)->uploadVideo($request);
});

Route::post('playlist', function(Request $request){
    return (new YoutubeService)->createPlaylist($request->title);
});

Route::get('analytics', function(){
    return (new YoutubeService)->getVideosViews();
});

Route::get('days', fn()=> response()->json([
    "record" => AttendanceService::mark(User::first())
]));

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

        Route::get('timeline', TimelineController::class);

        Route::prefix('auth')->group(function(){
            // system wide notifications
            Route::get("notifications", [NotificationsController::class, 'index']);
            // Mark notification as read
            Route::put("notifications/{notificaition}/read", [NotificationsController::class, 'markAsRead']);
            // award point to the user 
            // knowledgebase resource
            Route::get('knowledgebase/resources', KnowledgebaseController::class);
        });


        Route::post('password/change', [PasswordController::class, 'changePassword']); 
        
        // Student Routes
        Route::prefix('auth/user')->group(function(){
            // User Logout
            Route::post('logout', fn() => (new LoginController)->logout());
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
            // Increment View Count for a partitcular lesson
            Route::put('classroom/lessons/{lesson}/views', [StudentClassroomController::class, 'incrementViewCount']);
            // Mark attendance for a meeting
            Route::put('classroom/meeting/{meeting}/student/{userId}', [StudentClassroomController::class, 'markAttendance']);
            // State of the union meeting routes
            Route::get('classroom/sotu', [StudentClassroomController::class, 'getSotu']);
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
            Route::post('logout', fn() => (new LoginController)->logout());
            // Change password Route
            Route::post('password/change', [PasswordController::class, 'changePassword']);
            // Profile Route
            Route::get('profile', [AdminProfileController::class, 'index']);
            // Change Proflie Settings Route
            Route::post('profile', [AdminProfileController::class, 'storeSettings']);

            // Onboarding Routes
            Route::post('onboard/facilitator', [OnboardingController::class, 'facilitator']);
            // onboard all students
            Route::post('onboard/students', [OnboardingController::class, 'students']);
            // onboard mentor
            Route::post('onboard/mentors', [OnboardingController::class, 'mentors']);
            // send magic link to all the users
            Route::post('onboard/send-login-link', [OnboardingController::class, 'sendMagicLinkToStudents']);
            // send magic link to single user
            Route::post('onboard/students/magic-link', [OnboardingController::class, 'sendMagicLink']);
            // send slack invite link to students
            Route::post('onboard/students/slack-invite', [OnboardingController::class, 'sendSlackInvite']);
            // change single user track
            Route::post('onboard/students/change-track', [TrackChangeController::class, 'update']);
            // change bulk users track
            Route::post('onboard/students/bulk-change-track', [OnboardingController::class, 'bulkChangeTrack']);
            // Get course details
            Route::get('courses', [AdminCourseController::class, 'index']);
            // update course details
            Route::put('course/{course}', [AdminCourseController::class, 'update']);
            // Send slack invite to single user
            Route::post('onboard/students/single-slack-invite', [OnboardingController::class, 'sendStudentSlackInvite']);
            // update student curriculum
            Route::post('lessons/{lesson}', [AdminController::class, 'updateCurriculum']);
             // update student curriculum for all lessons
            Route::post('classroom', [AdminController::class, 'updateCourseContent']);
        });

        // Facilitator Routes
        Route::prefix('auth/facilitator')->group(function(){
            Route::post('logout', fn() => (new LoginController)->logout());
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
            // stage lesson route
            Route::post('classroom/stage', [FacilitatorClassroomController::class, 'stageLesson']);
            // Get Single Lesson details
            Route::get("classroom/{lesson}", [FacilitatorClassroomController::class, 'showLesson']);
            // save lesson as draft
            Route::post("classroom/draft", [FacilitatorClassroomController::class, 'saveAsDraft']);
            // Get facilitator schedule
            Route::get('schedule', [FacilitatorScheduleController::class, 'index']);
            // Create a new meeting
            Route::post('meetings', [FacilitatorScheduleController::class, 'fixLiveClass']);
            // Get Tasks Route
            Route::get('tasks', [FacilitatorTaskController::class, 'index']);
            // Get particular task with students that have submitted theirs
            Route::get('tasks/{task}', [FacilitatorTaskController::class, 'viewSubmissions']);
            // create lesson task route
            Route::post('tasks/{lesson}', [FacilitatorTaskController::class, 'store']); 
            // update lesson task route
            Route::put('tasks/{task}', [FacilitatorTaskController::class, 'update']);
            // grade lesson task
            Route::put('tasks/{task}/grade/{student}', [FacilitatorTaskController::class, 'gradeTask']);
            // close submission for a particular task
            Route::put('tasks/{task}/close', [FacilitatorTaskController::class, 'closeSubmission']);
            // Mark lesson task as graded
            Route::put('tasks/{task}/graded', [FacilitatorTaskController::class, 'markTaskAsGraded']);
            // Student Leaderboard route
            Route::get('students/leaderboard', [FacilitatorsStudentPerformanceControler::class, 'index']);
            // Add bonus point to student
            Route::put('students/leaderboard/{user}/points/bonus', [FacilitatorsStudentPerformanceControler::class, 'addBonusPointToStudent']);
            // Student mentor route
            Route::get('mentors', [StudentMentorsController::class, 'index']);
            // Assign mentee to mentor
            Route::put('mentors/{mentor}/mentees/{user}', [StudentMentorsController::class, 'assignMenteeToMentor']);
            // Remove mentee from mentor
            Route::delete('mentors/{mentor}/mentees/{user}', [StudentMentorsController::class, 'removeMenteeFromMentor']);
            // Award points to the user
            Route::post("points/{user}", [PointController::class, 'awardPoints']);
        });

        // Mentor Routes
        Route::prefix('auth/mentor')->group(function(){
            Route::post('auth/logout', fn() => (new LoginController)->logout());
        
            // Create Password Route
            Route::post('auth/mentor/password/create', [PasswordController::class, 'createPassword']);
            // Profile Route
            Route::get('profile', [MentorProfileController::class, 'index']);
            // Change Proflie Settings Route
            Route::post('profile', [MentorProfileController::class, 'storeSettings']);

        });
    });


});
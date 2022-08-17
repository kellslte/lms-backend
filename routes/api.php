<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\MagicLoginController;
use App\Http\Controllers\Admin\OnboardingController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Student\TaskController as StudentTaskController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Mentor\ProfileController as MentorProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\ClassroomController as StudentClassroomController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Facilitator\ProfileController as FacilitatorProfileController;
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

            // Classroom Routes
            Route::get('classroom', [StudentClassroomController::class, 'index']);
            // Get Student Lessons
            Route::get('classroom/lessons', [StudentClassroomController::class, 'getStudentLessons']);
            // Get Single Lesson from classroom
            Route::get('classroom/lessons/{lesson}', [StudentClassroomController::class, 'getLessons']);
            // Task Routes
            Route::get('tasks', [StudentTaskController::class, 'index']);
            // Submit Task
            Route::post('tasks/{task}', [StudentTaskController::class, 'submit']);
        });

        // Admin Routes
        Route::prefix('auth/admin')->group(function(){
            // Admin Logour
            Route::post('logout', fn() => (new LoginController)->logout('admin'));
            // Admin Token Refresh
            Route::post('password/create', [PasswordController::class, 'createPassword']);
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
            // Create Password Route
            Route::post('password/create', [PasswordController::class, 'createPassword']);
            // Profile Route
            Route::get('profile', [FacilitatorProfileController::class, 'index']);
            // Change Proflie Settings Route
            Route::post('profile', [FacilitatorProfileController::class, 'storeSettings']);

            // Class Room Routes

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

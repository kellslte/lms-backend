<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Student\Auth\PaymentController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Student\Auth\RegisterController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Student\Auth\MagicLoginController;
use App\Http\Controllers\Admin\Auth\AdminPasswordController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Student\Auth\StudentLoginController;
use App\Http\Controllers\Student\Auth\StudentPasswordController;

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
    Route::middleware('guest:student')->get('auth/magic/login/{token}', MagicLoginController::class)->name('verify-login');

    Route::post('auth/magic/send-token', function(Request $request){
        $request->validate(['email' => 'required|email']);

        User::whereEmail($request->email)->firstOrFail()->sendMagicLink();

        return response()->json([
            'status' => 'success',
            'message' => 'Check your email inbox for a link to login'
        ]);
    });

    Route::post('contact-us', ContactController::class);

    // Regular Authentication Routes
    // Route::post('auth/admin/login', [LoginController::class, 'adminLogin']);
    // Route::post('auth/mentor/login', [LoginController::class, 'mentorLogin']);
    // Route::post('auth/facilitator/login', [LoginController::class, 'facilitatorLogin']);
    // Route::post('auth/help-desk/login', [LoginController::class, 'helpDeskLogin']);
    
    
    // Student Registration Routes
    Route::post('auth/login', [LoginController::class, 'login']);
    Route::post('auth/register', [RegisterController::class, 'register']);
    Route::get('auth/tracks', [RegisterController::class, 'getTracksAndCourses']);
    
    Route::post('auth/student/transaction/complete', PaymentController::class);
    
    // Password Reset Routes
    Route::post('auth/password/{user}/send-reset-link', [PasswordController::class, 'requestPasswordReset']);
    
    Route::post('auth/password/{user}/reset', [PasswordController::class, 'resetPassword']);
    
    // Protected Routes
    
    Route::middleware('auth:student')->group(function(){        
        Route::prefix('auth/user')->group(function(){
            // User Logout
            Route::post('logout', [StudentLoginController::class, 'logout']);
            // Token Refresh
            Route::post('refresh', [StudentLoginController::class, 'refresh']);
            // Create New User Password
            Route::post('password/create', StudentPasswordController::class);
        });

        Route::prefix('user')->group(function(){

        });
        
    });

    
    Route::middleware('auth:admin')->group(function(){

        Route::prefix('auth/admin')->group(function(){
            // Admin Logour 
            Route::post('logout', [AdminLoginController::class, 'logout']);
            // Admin Token Refresh
            Route::post('refresh', [AdminLoginController::class, 'refresh']);
            // Admin Create Password Route
            Route::post('password/create', AdminPasswordController::class);
        });

        Route::prefix('admin')->group(function(){
            // Create Facilitator

            // Create Mentor

            // Create Help Desk User


        });
    });

    Route::middleware('auth:facilitator')->group(function(){
        Route::post('auth/logout', [LoginController::class, 'logout']);
    
        Route::post('auth/refresh', [LoginController::class, 'refresh']);

        // Create Password Route
        Route::post('auth/facilitator/password/create', [PasswordController::class, 'createNewPassword']);
        
        // User Creation Routes
        
    });

    Route::middleware('auth:mentor')->group(function(){
        Route::post('auth/logout', [LoginController::class, 'logout']);
    
        Route::post('auth/refresh', [LoginController::class, 'refresh']);

        // Create Password Route
        Route::post('auth/mentor/password/create', [PasswordController::class, 'createNewPassword']);
        
        // User Creation Routes
        
    });

    Route::middleware('auth:help-desk-user')->group(function(){
        Route::post('auth/logout', [LoginController::class, 'logout']);
    
        Route::post('auth/refresh', [LoginController::class, 'refresh']);

        // Create Password Route
        Route::post('auth/support/password/create', [PasswordController::class, 'createNewPassword']);
        
        // User Creation Routes
        
    });


});

Route::get('send-login-link', function(Request $request){
    User::whereEmail($request->email)->firstOrFail()->sendMagicLink();
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Student\Auth\PaymentController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Student\Auth\RegisterController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Auth\MagicLoginController;
use App\Http\Controllers\Admin\Auth\AdminPasswordController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;

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
    Route::middleware('guest')->get('auth/magic/login/{token}', [MagicLoginController::class, 'checkUserAndRedirect'])->name('verify-login');

    Route::post('auth/magic/send-token', [MagicLoginController::class, 'sendLoginLink']);

    // Student Registration Routes
    Route::post('auth/login', [LoginController::class, 'checkUserLogin']);

    // Password Reset Routes
    Route::post('auth/password/{user}/send-reset-link', [PasswordController::class, 'checkUserIdentity']);

    Route::post('auth/password/{user}/reset', [PasswordController::class, 'checkUserIdentityForReset']);


    // Protected Routes
    Route::middleware('auth:sanctum')->group(function(){
        Route::prefix('auth/user')->group(function(){
            // User Logout
            Route::post('logout', fn() => (new LoginController)->logout('student'));
            // Token Refresh
            Route::post('refresh', fn() => (new LoginController)->refresh('student'));
            // Create New User Password
            Route::post('password/create', [PasswordController::class, 'createPassword']);
        });

        Route::prefix('user')->group(function(){
            // Dashboard Route
            Route::get('dashboard', [StudentDashboardController::class, 'index']);
        });

    });


    Route::middleware('auth:sanctum')->group(function(){

        Route::prefix('auth/admin')->group(function(){
            // Admin Logour
            Route::post('logout', fn() => (new LoginController)->logout('admin'));
            // Admin Token Refresh
            Route::post('refresh', fn() => (new LoginController)->refresh('admin'));
            // Admin Create Password Route
            Route::post('password/create', [PasswordController::class, 'createPassword']);
        });

        Route::prefix('admin')->group(function(){
            // Create Facilitator

            // Create Mentor

            // Create Help Desk User


        });
    });

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('auth/logout', fn() => (new LoginController)->logout('facilitator'));

        Route::post('auth/refresh', fn() => (new LoginController)->refresh('facilitator'));

        // Create Password Route
        Route::post('auth/facilitator/password/create', [PasswordController::class, 'createPassword']);

        // User Creation Routes

    });

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('auth/logout', fn() => (new LoginController)->logout('mentor'));

        Route::post('auth/refresh', fn() => (new LoginController)->refresh('mentor'));

        // Create Password Route
        Route::post('auth/mentor/password/create', [PasswordController::class, 'createPassword']);

        // User Creation Routes

    });


});

Route::get('send-login-link', function(Request $request){
    User::whereEmail($request->email)->firstOrFail()->sendMagicLink();
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

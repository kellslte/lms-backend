<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PaymentController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\MagicLoginController;

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
    Route::get('auth/magic/login/{token}', MagicLoginController::class)->name('verify-login');

    // Regular Authentication Routes
    Route::post('auth/login', [LoginController::class, 'login']);

    Route::post('auth/logout', [LoginController::class, 'logout']);

    Route::post('auth/refresh', [LoginController::class, 'refresh']);

    // Student Registration Routes
    Route::post('auth/register', [RegisterController::class, 'register']);
    Route::get('auth/tracks', [RegisterController::class, 'getTracksAndCourses']);

    Route::post('auth/transaction/complete', PaymentController::class);

    // Password Reset Routes
    Route::post('auth/password/send-reset-link', [PasswordController::class, 'requestPasswordReset']);

    Route::post('auth/password/reset', [PasswordController::class, 'resetPassword']);

    // Protected Routes
    Route::middleware('auth:api')->group(function(){
        // Create Password Route
        Route::post('auth/password/create', [PassworDController::class, 'createNewPassword']);

        // User Creation Routes
        
    });
});

Route::get('send-login-link', function(){
    User::whereEmail('maxotif@gmail.com')->firstOrFail()->sendMagicLink();
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
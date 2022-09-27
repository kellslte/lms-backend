<?php

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Services\ProcessVideo;
use App\Jobs\ProcessVideoUpload;
use App\Services\YoutubeService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// // Magic Link Login
Route::middleware('guest')->get('auth/magiclink/{token}', fn($token) => response()->json([
    'status' => 'success',
    'token' => $token,
]))->name('magic-login');

Route::get('auth', function(){
    $client = new YoutubeService();

    return $client->getGoogleAuth();
});

Route::get('redirect', function(Request $request){
    $client = new YoutubeService();

    return $client->authenticateUser($request);
});

Route::get('playlist', function(Request $request){
    $playlistId = Http::post("localhost:8000/api/playlist", [
        "title" => "Product Design"
    ]);

    dd($playlistId);
});

Route::get('students', function(){
    $students = User::all();

    $students->load("course");

    $response = collect($students)->map(function($student){
        return [
            "course" => $student->course->title,
            "studentId" => $student->id,
            "studentName" => $student->name
        ];
    })->groupBy("course");

    return response()->json([
        "status" => "successful",
        "data" => [
            "records" => $response
        ]
    ]);
});
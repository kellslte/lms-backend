<?php

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\PaymentGatewayRequest;
use Unicodeveloper\Paystack\Facades\Paystack;
use alchemyguy\YoutubeLaravelApi\VideoService;
use App\Http\Controllers\Auth\PaymentController;
use App\Http\Controllers\Auth\MagicLoginController;
use  alchemyguy\YoutubeLaravelApi\AuthenticateService;

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

// Magic Link Login
Route::middleware('guest')->get('auth/magiclink/{token}', fn($token) => response()->json([
    'status' => 'success',
    'token' => $token,
]))->name('verify-login');

Route::get('ordinal', fn() => response()->json(ordinal(41)));

Route::get('youtube', function(){
    $authObject  = new AuthenticateService;

    # Replace the identifier with a unqiue identifier for account or channel
    $authUrl = $authObject->getLoginUrl('email', 'UCtaa9WH19QmP2sIkqQXXDgw');

    return redirect($authUrl);
});

Route::get('redirect', function(Request $request){
    $code = $request->get('code');
    $identifier = $request->get('state');

    $authObject = new AuthenticateService;
    $authResponse = $authObject->authChannelWithCode($code);

    $tokens = collect($authResponse);

    return Cache::forever("youtube-tokens", $tokens);
});

Route::get('videos', function(){
    $part = 'snippet, contentDetails, id, statistics';

    $videosService = new VideoService;
    
    $response = $videosService->videosListById($part, ['id' => 'xyzgh']);

    $tokens = collect(Cache::get("youtube-tokens"));

    dd($response, $tokens["token"]["refresh_token"]);
});

Route::post('video', function(Request $request){
    $token = collect(Cache::get("youtube-tokens"))["token"]["refresh_token"];

    $videoServiceObject  = new VideoService;

    $data = [
        "title" => $request->title,
        "description" => $request->description,
    ];

    $response = $videoServiceObject->uploadVideo($token, $request->file('video'), $data);

    dd($response);
});

Route::get('token', function(){
    $url = "https://oauth2.googleapis.com/token";
    $token = collect(Cache::get("youtube-tokens"))["token"]["refresh_token"];

    $response = Http::asForm()->post($url, [
        "data" => [
            "refresh_token" => $token,
            "client_id" => config('google-config.client_id'),
            "client_secret" => config('google-config.client_secret'),
            "grant_type" => "refresh_token",
        ]
    ]);

    dd($response);
});




Route::get('lessons', function(){
    $lessons = [];
    $course = Course::firstWhere('title', 'Product Design');

    foreach ($course->lessons as $lesson){
        $lessons[] = [
            "lesson_id" => $lesson->id,
            "lesson_status" => "uncompleted"
        ];
    }

    return response()->json(json_encode($lessons));
});
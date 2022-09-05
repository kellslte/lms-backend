<?php

use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\YoutubeService;
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

Route::get('auth', function(){
    $client = new YoutubeService();

    return $client->getGoogleAuth();
});

Route::get('redirect', function(Request $request){
    $client = new YoutubeService();

    return $client->authenticateUser($request);
});

Route::get('token', function(){
    $client = new YoutubeService();

    dd($client->listVideos("r_l2SgphnOI"));
});


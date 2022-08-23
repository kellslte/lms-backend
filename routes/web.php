<?php

use Illuminate\Support\Facades\Route;
use App\Http\Requests\PaymentGatewayRequest;
use Unicodeveloper\Paystack\Facades\Paystack;
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
});
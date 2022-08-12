<?php

use Illuminate\Support\Facades\Route;
use App\Http\Requests\PaymentGatewayRequest;
use Unicodeveloper\Paystack\Facades\Paystack;
use App\Http\Controllers\Auth\PaymentController;
use App\Http\Controllers\Auth\MagicLoginController;

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
Route::middleware('guest')->get('auth/magiclink/{token}', [MagicLoginController::class, 'checkUserAndRedirect'])->name('verify-login');
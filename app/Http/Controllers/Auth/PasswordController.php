<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\CreatePasswordRequest;
use Illuminate\Auth\Events\PasswordReset;

class PasswordController extends Controller
{
    public function requestPasswordReset(Request $request, $user){
        $request->validate(['email' => 'required|email']);

        $status = Password::broker($user)->sendResetLink($request->only('email'));

        if($status === Password::RESET_LINK_SENT){
            return response()->json([
                'status' => 'success',
                'message' => 'Link  has been sent to email address',
            ], 200);
        }   
        
        return response()->json([
            'status' => 'failed',
            'message' => 'Email is not registered'
        ], 422);
    }

    public function resetPassword(ResetPasswordRequest $request, $user){

        $status = Password::broker($user)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function($user, $password){
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if($status === Password::PASSWORD_RESET){
            return response()->json([
                'status' => 'success',
                'message' => 'Your password was reset successfully.'
            ], 200);
        }
        
        return response()->json([
            'status' => 'failed',
            'message' => 'Your password could not be reset, please try again'
        ], 422);
    }
}

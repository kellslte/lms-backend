<?php

namespace App\Http\Controllers\Student\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentLoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        $token = Auth::guard('student')->attempt($credentials);

        if (!$token) return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);

        $user = User::whereEmail($credentials['email'])->firstOrFail();

        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ],
            'data' => [
                'user' => $user,
                'course' => $user->course->title,
                'track' => $user->course->track->title,
                'role' => 'student'
            ]
        ]);
    }

    public function logout()
    {
        Auth::guard('student')->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::guard('student')->user(),
            'authorisation' => [
                'token' => auth('student')->refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}

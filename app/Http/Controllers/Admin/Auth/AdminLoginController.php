<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{

    public function login(LoginRequest $request)
    {
        $request->only(['email', 'password']);

        $admin = Admin::whereEmail($request->email)->firstOrFail();

        $token = Auth::guard('admin')->login($admin);

        if (!$token) return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);

        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ],
            'user' => $admin,
            'role' => 'admin'
        ]);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::guard('admin')->user(),
            'authorisation' => [
                'token' => auth('admin')->refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}

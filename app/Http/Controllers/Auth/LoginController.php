<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Facilitator;
use Illuminate\Http\Request;
use App\Services\LoginService;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Services\LogoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function checkUserLogin(LoginRequest $request){
        return LoginService::redirectBasedOnIdentity($request);
    }

    public function checkUserLogout(){
        return LogoutService::redirectBasedOnIdentity();
    }

    public function adminLogin($request){
        $request->only(['email', 'password']);

        if (!$admin = Admin::whereEmail($request->email)->first())
            return response()->json([
                'status' => 'failed',
                'message' => 'Email or password incorrect'
            ]);

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

    public function mentorLogin($request){
        $request->only(['email', 'password']);

        if(!$mentor = Mentor::whereEmail($request->email)->first())
            return response()->json([
                'status' => 'failed',
                'message' => 'Email or password incorrect'
            ]);

        $token = Auth::guard('mentor')->login($mentor);

        if(!$token) return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);

        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ],
            'user' => $mentor,
            'role' => 'mentor'
        ]);
    }

    public function facilitatorLogin($request){
        $request->only(['email', 'password']);

        if (!$facilitator = Facilitator::whereEmail($request->email)->first())
            return response()->json([
                'status' => 'failed',
                'message' => 'Email or password incorrect'
            ]);
        

        $token = Auth::login($facilitator);

        if(!$token) return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);

        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ],
            'user' => $facilitator,
            'role' => 'facilitator'
        ]);
    }

    public function login($request)
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

    public function logout($guard)
    {
        Auth::guard($guard)->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh(Request $request){
        return response()->json([
            'status' => 'success',
            'user' => Auth::guard($request->guard)->user(),
            'authorisation' => [
                'token' => auth($request->guard)->refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}

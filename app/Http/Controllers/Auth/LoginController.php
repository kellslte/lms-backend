<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Facilitator;
use Illuminate\Http\Request;
use App\Services\LoginService;
use App\Services\LogoutService;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

        Auth::guard('admin')->login($admin);

        $token  = $admin->createToken('access_token');

        if (!$token) return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);

        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => $token->plainTextToken,
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
            
        Auth::guard('mentor')->login($mentor);

        $token = $mentor->createToken('access_token');

        if(!$token) return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);

        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => $token->plainTextToken,
                'type' => 'bearer'
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
        

        Auth::guard('facilitator')->login($facilitator);

        $token = $facilitator->createToken('access_token');

        if(!$token) return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);

        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => $token->plainTextToken,
                'type' => 'bearer',
            ],
            'user' => $facilitator,
            'role' => 'facilitator'
        ]);
    }

    public function login($request)
    {
        $credentials = $request->only(['email', 'password']);

        Auth::guard('student')->attempt($credentials);

        $user = User::whereEmail($credentials['email'])->first();

        $token = $user->createToken('access_token');

        $request->merge([
            'user' => $user,
        ]);

        if (!$token) return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);


        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => $token->plainTextToken,
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

        request()->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
            'user' => auth($guard)->user(),
        ]);
    }
}

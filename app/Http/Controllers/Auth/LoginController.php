<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Facilitator;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\HelpDeskUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function mentorLogin(LoginRequest $request){
        $request->only(['email', 'password']);

        $mentor = Mentor::whereEmail($request->email)->firstOrFail();

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

    public function facilitatorLogin(LoginRequest $request){
        $request->only(['email', 'password']);

        $facilitator = Facilitator::whereEmail($request->email)->firstOrFail();

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

    public function helpDeskLogin(LoginRequest $request){
        $request->only(['email', 'password']);

        $helpDesk = HelpDeskUser::whereEmail($request->email)->firstOrFail();

        $token = Auth::guard('help-desk-user')->login($helpDesk);

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
            'user' => $helpDesk,
            'role' => 'help desk user'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard($request->guard)->logout();
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

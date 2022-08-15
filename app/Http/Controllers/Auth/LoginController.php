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
                'message' => 'User record does not exist'
            ], 404);

        if(Hash::check($request->password, $admin->password)){
            $token  = $admin->createToken('access_token');
    
            if (!$token) return response()->json([
                'status' => 'failed',
                'message' => 'Email or password incorrect',
            ], 401);
    
            return response()->json([
                'status' => 'success',
                'token' => $token->plainTextToken,
                'data' => [
                    'user' => $admin,
                    'role' => 'admin'
                ],
            ]);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);
    }

    public function mentorLogin($request){
        $request->only(['email', 'password']);

        if(!$mentor = Mentor::whereEmail($request->email)->first())
            return response()->json([
                'status' => 'failed',
                'message' => 'User record does not exist',
            ], 404);
            
        // Check password
        if(Hash::check($request->password, $mentor->password)){
            $token = $mentor->createToken('access_token');
    
            if(!$token) return response()->json([
                'status' => 'failed',
                'message' => 'Email or password incorrect',
            ], 401);

            return response()->json([
                    'status' => 'success',
                    'token' => $token->plainTextToken,
                    'data' => [
                        'user' => $mentor,
                        'role' => 'mentor'
                    ],
                ]);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Email or password incorrect',
        ], 401);
    }

    public function facilitatorLogin($request){
        $request->only(['email', 'password']);

        if (!$facilitator = Facilitator::whereEmail($request->email)->first())
            return response()->json([
                'status' => 'failed',
                'message' => 'User record does not exist',
            ], 404);
        
            // check password 
        if(Hash::check($$request->password, $facilitator->password)){
            $token = $facilitator->createToken('access_token');
    
            if(!$token) return response()->json([
                'status' => 'failed',
                'message' => 'Email or password incorrect',
            ], 400);

            return response()->json([
                    'status' => 'success',
                    'token' => $token->plainTextToken,
                    'data' => [
                        'user' => $facilitator,
                        'role' => 'facilitator'
                    ],
                ]);
        }

       return response()->json([
        'status' => 'failed',
        'message' => 'Email or password incorrect',
       ], 401);
    }

    public function login($request)
    {
        
        if(!$user = User::whereEmail($request->email)->first()){
            return response()->json([
                'status' => 'failed',
                'message' => 'User record does not exist',
            ], 404);
        };

        if(Hash::check($request->password, $user->password)){
            $token = $user->createToken('access_token');
    
            if (!$token) return response()->json([
                'status' => 'failed',
                'message' => 'Email or password incorrect',
            ], 401);
    
            return response()->json([
                'status' => 'success',
                'token' => $token->plainTextToken,
                'data' => [
                    'user' => $user,
                    'course' => $user->course->title,
                    'track' => $user->course->track->title,
                    'role' => 'student'
                ]
            ]);
        }

    }

    public function logout($guard)
    {
        Auth::guard($guard)->logout();

        $user = getAuthenticatedUser();

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
            'user' => auth($guard)->user(),
        ]);
    }
}

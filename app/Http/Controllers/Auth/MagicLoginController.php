<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Admin;
use App\Models\Mentor;
use App\Models\MagicToken;
use App\Models\Facilitator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

class MagicLoginController extends Controller
{
    public function checkUserAndRedirect(Request $request, $token)
    {
        $dbtoken = MagicToken::whereToken(hash('sha256', $token))->firstOrFail();

        if(!$request->hasValidSignature() && !$dbtoken->isValid()){
            return response()->json([
                'status' => 'failed',
                'message' => 'This login link has expired. Please resend a link and try again.',
            ], 401);
        };

        $dbtoken->consume();

        switch($dbtoken->tokenable->email){
            case strpos($dbtoken->tokenable->email, '.facilitator') !== false:
                return $this->facilitatorLogin($dbtoken->tokenable);
                break;

            case strpos($dbtoken->tokenable->email, '.admin') !== false:
                return $this->adminLogin($dbtoken->tokenable);
                break;

            case strpos($dbtoken->tokenable->email, '.mentor') !== false:
                return $this->mentorLogin($dbtoken->tokenable);
                break;

            default:
                return $this->login($dbtoken->tokenable);
                break;
        }
    }

    public function sendLoginLink(Request $request){
        $request->validate(['email' => 'required|email']);


        switch ($request->email) {
            case strpos($request->email, '.facilitator') !== false:
                return $this->sendLink(Facilitator::firstWhere('email', $request->email));
                break;

            case strpos($request->email, '.admin') !== false:
                return
                $this->sendLink(Admin::firstWhere('email', $request->email));;
                break;

            case strpos($request->email, '.mentor') !== false:
                return
                $this->sendLink(Mentor::firstWhere('email', $request->email));;
                break;

            default:
                return
                $this->sendLink(User::firstWhere('email', $request->email));;
                break;
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function login($user)
    {
        auth()->guard('student')->login($user);

        $token = $user->createToken('access_token');

        return response()->json([
            'token' => $token->plainTextToken,
            'data' => [
                'user' => $user,
                'course' => $user->course->title,
                'track' => $user->course->track->title,
                'role' => 'student'
            ]
        ]);
    }

    private function adminLogin($admin)
    {
        auth()->guard('admin')->login($admin);

        $token = $admin->createToken('access_token');

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

    private function mentorLogin($mentor)
    {
        auth()->guard('mentor')->login($mentor);

        $token =  $mentor->createToken('access_token');

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
            'user' => $mentor,
            'role' => 'mentor'
        ]);
    }

    private function facilitatorLogin($facilitator)
    {
        auth()->guard('facilitator')->login($facilitator);

        $token = $facilitator->createToken('access_token');

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
            'user' => $facilitator,
            'role' => 'facilitator'
        ]);
    }

    private function sendLink(Model $user){
        
        try{
            $user->sendMagicLink();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Check your email inbox for a link to login'
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong and we could not send your link. Please try again later.'
            ], 400);
        }
    }

}

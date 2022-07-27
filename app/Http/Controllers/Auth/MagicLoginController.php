<?php

namespace App\Http\Controllers\Auth;

use App\Models\MagicToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MagicLoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $token)
    {
        $dbtoken = MagicToken::whereToken(hash('sha256', $token))->firstOrFail();

        abort_unless($request->hasValidSignature() && $dbtoken->isValid(), 401);

        $dbtoken->consume();

        $token = Auth::login($dbtoken->user);

        $user = User::whereEmail($dbtoken->user->email)->firstOrFail();

        return response()->json([
            'token' => $token,
            'user' => $dbtoken->user,
            'role' => $user->roles[0]->name
        ]);
    }
}

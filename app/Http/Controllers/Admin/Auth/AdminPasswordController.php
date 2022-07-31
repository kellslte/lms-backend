<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\CreatePasswordRequest;

class AdminPasswordController extends Controller
{
    public function __invoke(CreatePasswordRequest $request)
    {
        $user = User::whereId(Auth::guard('student')->id())->firstOrFail();

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'password created successfully'
        ], 200);
    }
}

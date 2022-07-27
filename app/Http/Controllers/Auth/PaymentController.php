<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PaystackService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\PaymentGatewayRequest;
use App\Mail\SendWelcomeMailToNewUser;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentController extends Controller
{
    public function __invoke(PaymentGatewayRequest $request)
    {
        $user = User::where($request->email)->firstOrFail();

        if($request->transaction_status !== 'success'){
            return response()->json([
                'status' => 'failed',
                'message' => 'The payment was not successful please try again later', 
            ]);
        }

        $user->transaction->update([
            'status' => 'paid',
            'transaction_id' => $request->reference
        ]);

        // Send Success Message to user
        Mail::to($user->email)->queue(new SendWelcomeMailToNewUser($user));

        return response()->json([
            'status' => 'success',
            'message' => 'Payment has been successfully processed',
        ]);
    }
}

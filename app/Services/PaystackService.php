<?php
namespace App\Services;


use Illuminate\Http\Request;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaystackService {

    public static function redirectToGateway(Request $request){
        try {
            return Paystack::getAuthorizationUrl($request)->rediretcNow();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment gateway not reachable',
            ]);
        }
    }

    public static function handleCallback(){

        $paymentDetails = Paystack::getPaymentData();

        return response()->json([
            'details' => $paymentDetails
        ]);

        // $details = [
        //     'transaction_id' => $paymentDetails['data']['metadata']['invoiceId'],
        //     'status' => $paymentDetails['data']['status'],
        //     'amount' => $paymentDetails['data']['amount'],
        // ];

        // if($details['status'] == 'success') {
        //     // Create Transaction Details in Database
        //     $user->transaction()->create([
        //         'transaction_id' => $details['transaction_id'],
        //         'email' => $user->email,
        //         'amount' => $details['amount'],
        //         'status' => 'paid'
        //     ]);

        //     return response()->json([
        //         'status' => $details['status'],
        //         'message' => 'User payment was successfully processed.',
        //     ]);
        // }

        // return response()->json([
        //     'status' => $details['status'],
        //     'message' => 'User payment failed, please try again in a few minutes.',
        // ]);

    } 
}
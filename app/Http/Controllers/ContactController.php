<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ContactRequest;
use App\Mail\SendContactMailToHelpDesk;

class ContactController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ContactRequest $request)
    {
        $data = [
            "name" => $request->name,
            "email" => $request->email,
            "subject" => $request->subject,
            "message" => $request->message,
        ];

        try {
            Mail::to("theadaproject@enugustatetechhub.en.gov.ng")->send(new SendContactMailToHelpDesk($data));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Your message has been received. Expect a response from our team shortly'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Your message could not be sent. Please try again shortly.'
            ]);
        }

    }
}

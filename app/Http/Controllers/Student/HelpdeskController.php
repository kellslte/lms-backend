<?php

namespace App\Http\Controllers\Student;

use App\Models\Admin;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Mail\IssueReportEmail;
use App\Models\CommunityManager;
use App\Http\Controllers\Controller;
use App\Notifications\IssueReportNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class HelpdeskController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();

        $mentor = [];

        if($user->mentor) {
            $detail = $user->mentor->mentor->with('socials');
        }

        if($facilitator = $user->course->facilitator){
            $facilitator->load('socials');
        }


        return response()->json([
            'status' => 'success',
            'data' => [
                'facilitator' => $facilitator,
                'mentor' => $mentor,
                'community_manager' => CommunityManager::with('socials')->first(),
            ],
        ], 200);
    }

    public function report(Request $request){
        $user = getAuthenticatedUser();

        $request->validate([
            'subject' => 'required|string',
            'details' => 'required|string',
        ]);

        //$admins = Admin::all();

        // create report 
        $report = $user->reports()->create([
            'subject' => $request->subject,
            'details' => $request->details,
        ]);

        // send mail to adaproject
        Mail::to('theadaproject@enugutechhub.en.gov.ng')->send(new IssueReportEmail($report));

        // notify admin
        //Notification::send($admins, new IssueReportNotification($report));

         // return response
        return response()->json([
            'status' => 'success',
            'messgae' => 'Your issue has been filed and the appropriate admins notified. You will get a response from them as soon as possible.',
        ]);
        try {
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'messgae' => 'Your issue could not be filed correctly. Please try again'
            ], 400);
        }
    }
}

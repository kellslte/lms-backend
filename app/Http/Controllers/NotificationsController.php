<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $notifications = collect(getAuthenticatedUser()->notifications)->map(function($notification){
            return [
                "id" => $notification->id,
                "data" => $notification->data,
                "read_at" => $notification->read_at,
                "notifiable" => $notification->notifiable->id
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                "notifications" => $notifications,
            ]
        ]);
    }

    public function markAsRead($notification){

        if($notification = getAuthenticatedUser()->notifications->where("id", $notification)->first()){
            $notification->update([
                "read_at" => now(),
            ]);

            return response()->json([
                "success" => true,
                "message" => "You have read this notification"
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "You have not read this notification yet"
        ]);
    }
}

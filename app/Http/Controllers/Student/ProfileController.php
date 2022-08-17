<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeSettingsRequest;

class ProfileController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();

        if(!$user->has('settings')){
            $user->settings()->create();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'profile' => $user,
                'settings' => $user->settings
            ]
        ]);
    }

    public function storeSettings(ChangeSettingsRequest $request){
        $user = getAuthenticatedUser();

        if(is_null($user->settings)){
            $user->settings()->create();

            $user->settings()->update([
                'notification_preference' => $request->notificationsToMail,
                'text_message_preference' => $request->receiveTexts
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User settings have been successfully updated.',
            ]);
        }

        $user->settings()->update([
            'notification_preference' => $request->notificationsToMail,
            'text_message_preference' => $request->receiveTexts
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User settings have been successfully updated.',
        ]);
    }
}

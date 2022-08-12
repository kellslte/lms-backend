<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeSettingsRequest;

class ProfileController extends Controller
{
    public function index(){
        $user = getAuthenticatedUser();
        return response()->json([
            'status' => 'success',
            'data' => [
                'profile' => $user,
                'settings' => $user->settings
            ]
        ]);
    }

    public function storeSettings(ChangeSettingsRequest $request,){
        $user = getAuthenticatedUser();

        $settings = $user->settings;

        try {
            $settings->update([
                'notification_preference' => $request->notificationsToMail,
                'text_message_preference' => $request->receiveTexts
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Your profile settings have been updated successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error has occurred while updating your profile settings. Please try again'
            ]);
        }
    }
}

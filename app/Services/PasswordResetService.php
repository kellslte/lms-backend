<?php
namespace App\Services;

use App\Http\Controllers\Auth\PasswordController;

class PasswordResetService {
    public static function redirectBasedOnIdentity($request)
    {
        return self::checkUserIdentity($request);
    }

    private static function checkUserIdentity($request)
    {
        // TODO check email
        switch ($request->email) {
            case strpos($request->email, '.facilitator') !== false:
                return (new PasswordController)->resetPassword($request, "facilitators");
                break;

            case strpos($request->email, '.admin') !== false:
                return (new PasswordController)->resetPassword($request, "admins");
                break;

            case strpos($request->email, '.mentor') !== false:
                return (new PasswordController)->resetPassword($request, "mentors");
                break;

            default:
                return (new PasswordController)->resetPassword($request, "users");
                break;
        }
    }
}
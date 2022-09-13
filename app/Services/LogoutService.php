<?php
namespace App\Services;

use App\Http\Controllers\Auth\LoginController;

class LogoutService {
    public static function redirectBasedOnIdentity()
    {
        return self::checkUserIdentity();
    }

    private static function checkUserIdentity()
    {
        switch (auth()->user()->email) {
            case strpos(auth()->user()->email, '.facilitator') !== false:
                return (new LoginController)->logout("facilitator");
                break;

            case strpos(auth()->user()->email, '.admin') !== false:
                return (new LoginController)->logout("admin");
                break;

            case strpos(auth()->user()->email, '.mentor') !== false:
                return (new LoginController)->logout("mentor");
                break;

            default:
                return (new LoginController)->logout("student");
                break;
        }
    }
}
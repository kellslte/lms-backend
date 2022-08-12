<?php
namespace App\Services;

use App\Http\Controllers\Auth\LoginController;

class LoginService {

    public static function redirectBasedOnIdentity($request){
       return self::checkUserIdentity($request);
    }

    private static function checkUserIdentity($request){
        // TODO check email
        switch ($request->email) {
            case strpos($request->email, '.facilitator') !== false:
                return (new LoginController)->facilitatorLogin($request);
                break;
            
            case strpos($request->email, '.admin') !== false:
                return (new LoginController)->adminLogin($request);
                break;
            
            case strpos($request->email, '.mentor') !== false:
                return (new LoginController)->mentorLogin($request);
                break;
            
            default:
                return (new LoginController)->login($request);
                break;
        }

    }
}
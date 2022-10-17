<?php

namespace App\Helpers;

use Laravel\Sanctum\PersonalAccessToken;

class AuthHelper
{
    public static function getAuthenticatedUser()
    {
        $authorization = request()->header('authorization');
        [$idString, $hash] = explode('|', $authorization, 2);

        [$type, $id] = explode(' ', $idString, 2);

        $token = PersonalAccessToken::find((int)$id);

        return $token->tokenable;
    }

    public static function generatePassword($strenght = 13): string
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $randomString = '';

        for ($i = 0; $i < $strenght; $i++) {
            $character = $permitted_chars[mt_rand(0, strlen($permitted_chars) - 1)];
            $randomString .= $character;
        }

        return $randomString;
    }
}

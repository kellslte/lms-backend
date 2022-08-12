<?php

use Laravel\Sanctum\PersonalAccessToken;

function getAuthenticatedUser(){
    $authorization = request()->header('authorization');
    [$idString, $hash] = explode('|', $authorization, 2);

    [$type, $id] = explode(' ', $idString, 2);

    $token = PersonalAccessToken::find((int)$id);

    return $token->tokenable;
}

function generatePassword($strenght = 13){
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $randomString = '';

    for($i = 0; $i < $strenght; $i++){
        $character = $permitted_chars[mt_rand(0, strlen($permitted_chars) - 1)];
        $randomString .= $character;
    }

    return $randomString;
}
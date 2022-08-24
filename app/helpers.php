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

function ordinal(Int $number){
    $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return ($number) ? $number . $ends[$number % 10] : $number;

}

function formatDate($date){
    return date_format(date_create($date), 'jS M, Y');
}

function formatTime($time){
    return date_format(date_create($time), 'h:i a');
}
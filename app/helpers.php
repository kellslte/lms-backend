<?php

use Laravel\Sanctum\PersonalAccessToken;

function getAuthenticatedUser(){
    $authorization = request()->header('authorization');
    [$idString, $hash] = explode('|', $authorization, 2);

    [$type, $id] = explode(' ', $idString, 2);

    $token = PersonalAccessToken::find((int)$id);

    return $token->tokenable;
}
<?php

use App\Services\EventService;
use App\Services\YoutubeService;
use Laravel\Sanctum\PersonalAccessToken;

function getAuthenticatedUser()
{
    $authorization = request()->header('authorization');
    [$idString, $hash] = explode('|', $authorization, 2);

    [$type, $id] = explode(' ', $idString, 2);

    $token = PersonalAccessToken::find((int)$id);

    return $token->tokenable;
}

function generatePassword($strenght = 13)
{
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $randomString = '';

    for ($i = 0; $i < $strenght; $i++) {
        $character = $permitted_chars[mt_rand(0, strlen($permitted_chars) - 1)];
        $randomString .= $character;
    }

    return $randomString;
}

function ordinal(Int $number)
{
    $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return ($number) ? $number . $ends[$number % 10] : $number;
}

function formatDate($date)
{
    return date_format(date_create($date), 'jS M, Y');
}

function getDay($date)
{
    return date_format(date_create($date), 'D');
}

function getWeek($date)
{
    return date_format(date_create($date), 'W');
}

function getMonth($date)
{
    return date_format(date_create($date), 'M');
}

function formatToMonth($date)
{
    return date_format(date_create($date), 'j M');
}

function formatTime($time)
{
    return date_format(date_create($time), 'h:i a');
}

function jsonResponse(array $data, Int $code)
{
    return response()->json([
        "status" => $data["status"],
        "data" => $data["response"],
    ], $code);
}

function getYoutubeVideoDetails($request)
{
    $id = $request->id;
    return (new YoutubeService())->listVideos($id);
}

function updateYoutubeVideoDetails($request)
{
    return (new YoutubeService())->updateVideo($request);
}

function createEvent(array $details)
{
    return (new EventService)->createEvent($details);
}

function getDaysInMonth(Int $monthToAdd = 0)
{   
    $dates = [];
    $record = [];
    
    for($w = 0; $w <= $monthToAdd; $w++){
        $month = today()->addMonths($w)->format('m');
        $year = today()->addMonths($w)->format('Y');

        $monthToShow = today()->addMonths($w);
        $yearToShow = today()->addMonths($w)->format('Y');
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($i = 1; $i <= $days; $i++) {
            $dates[] = [
                "present" => false,
                "day" => ordinal($i) . " " . $monthToShow->format('M') . "_" . $yearToShow
            ];
        }

        array_push($record, $dates);
    }

    return $record[7];
}

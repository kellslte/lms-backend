<?php
namespace App\Services;

use Google\Client;
use App\Models\GoogleToken;
use Google\Service\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventService{
    protected $client;

    protected $scope = [
        'https://www.googleapis.com/auth/calendar',
    ];

    protected $service;


    public function __construct()
    {
        $this->client = new Client();

        $this->client->setClientId(config('services.youtube.id'));

        $this->client->setClientSecret(config('services.youtube.secret'));

        $this->client->setRedirectUri(config('services.youtube.redirecturi'));

        $this->client->setApplicationName(config('services.youtube.appName'));

        $this->client->setDeveloperKey(config('services.youtube.token'));

        $this->client->setAccessType("offline");

        $this->service = new Calendar($this->client);
    }

    public function getGoogleAuth()
    {
        $authUrl = $this->client->createAuthUrl($this->scope);

        return redirect($authUrl);
    }

    public function authenticateUser(Request $request)
    {
        $code = $request->code;

        $tokens = $this->client->fetchAccessTokenWithAuthCode($code);

        $this->storeTokens($tokens);
    }

    private function getTimeZone(){
        $token = $this->refreshToken();
    }


    private function refreshToken()
    {
        if (Cache::has('access_token') && !$this->client->isAccessTokenExpired()) {
            return Cache::get('access_token');
        }

        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = GoogleToken::all()->last();

            $tokens = collect($this->client->refreshToken($refreshToken->token));

            $token = $this->storeTokens($tokens);
        }

        return $token;
    }

    private function storeTokens($tokens)
    {
        $collection = json_decode(collect($tokens)->toJson(), true);

        // TODO store access token in cache
        Cache::put("access_token", $collection["access_token"], 1800);

        // TODO store refresh token in database
        GoogleToken::create([
            "token" => $collection['refresh_token'],
        ]);

        return Cache::get("access_token");
    }

    public function createEvent(Array $details){
        $this->refreshToken();

        // create the event and add attendees


    }

    public function listCalendarEvents(){}

    public function updateEvent(Array $details){}

    public function deleteEvent($eventId){}
}
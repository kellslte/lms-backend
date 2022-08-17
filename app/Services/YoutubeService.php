<?php
namespace App\Services;

use Google\Client;

class YoutubeService {

    protected static $client;
    protected static $language;

    public static function __constructStatic()
    {
        self::initialize();
    }

    protected static function initialize(){
        self::$client = new Client();
        // set auth credentials
        self::$client->setClientId(config('services.youtube.id'));
        self::$client->setClientSecret(config('services.youtube.secret'));
        self::$client->setDeveloperKey(config('services.youtube.token'));
        self::$client->setRedirectUri(config('services.youtube.redirecturi'));

        // set cliient scopes
        self::$client->setScopes([
            'https://www.googleapis.com/auth/youtube',
        ]);

        // setup remaining configuration
        self::$client->setAccessType('offline');
        self::$client->setPrompt('consent');
        self::$language = config('services.youtube.language');
    }

    public static function listVideos(){}

    public static function getVideo(){}

    public static function streamVideo(){}

    public static function addVideoComment() {}

    public static function getVideoComments() {}
}
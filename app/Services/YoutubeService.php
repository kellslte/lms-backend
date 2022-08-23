<?php
namespace App\Services;

use alchemyguy\YoutubeLaravelApi\ChannelService;
use  alchemyguy\YoutubeLaravelApi\AuthenticateService;
use  alchemyguy\YoutubeLaravelApi\VideoService;	

class YoutubeService {

    protected static $client;
    protected static $language;
    protected static $channelService;
    protected static $videoService;

    public static function __constructStatic()
    {
        self::initialize();
    }

    protected static function initialize(){
        self::$client = new AuthenticateService;

        // Change the 'identifier' property once you pull this from Google Devs console
        $authUrl = self::$client->getLoginUrl('email', 'identifier');

        self::$channelService = new ChannelService;
        self::$videoService =  new VideoService;
    }

    public function setAuth(){
        
    }

    public static function listVideos(String $channelID){
        $part = 'id,snippet';

        $params = [
            'id' => $channelID,
        ];

        return self::$channelService->listVideos($part, $params);
    }

    public static function getVideo(String $videoId){
        $part = 'snippet,contentDetails,id,statistices';
        $params = [ 'id' => $videoId ];

        return self::$videoService->videsoListById($part, $params);
    }

    public static function streamVideo(){}

    public static function addVideoComment() {}

    public static function getVideoComments() {}
}
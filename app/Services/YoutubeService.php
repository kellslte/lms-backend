<?php
namespace App\Services;

use Google\Client;
use App\Models\Course;
use App\Models\GoogleToken;
use Google\Service\YouTube;
use Illuminate\Http\Request;
use Google\Http\MediaFileUpload;
use Google\Service\YouTube\Video;
use Google\Service\YouTube\Playlist;
use Illuminate\Support\Facades\Cache;
use Google\Service\YouTube\ResourceId;
use Google\Service\YouTube\VideoStatus;
use Google\Service\YouTube\PlaylistItem;
use Google\Service\YouTube\VideoSnippet;
use Google\Service\YouTube\PlaylistStatus;
use Google\Service\YouTube\PlaylistSnippet;
use Google\Service\AnalyticsReporting\Report;
use Google\Service\YouTube\PlaylistItemSnippet;
use Google\Service\AnalyticsReporting\ReportData;

class YoutubeService {
    protected $client;

    protected $scope = [
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/youtube.upload',
        'https://www.googleapis.com/auth/youtube.readonly',
        'https://www.googleapis.com/auth/youtube.force-ssl',
        'https://www.googleapis.com/auth/youtubepartner',
        'https://www.googleapis.com/auth/yt-analytics.readonly'

    ];

    protected $service;


    public function __construct(){
        $this->client = new Client();

        $this->client->setClientId(config('services.youtube.id'));

        $this->client->setClientSecret(config('services.youtube.secret'));

        $this->client->setRedirectUri(config('services.youtube.redirecturi'));

        $this->client->setApplicationName(config('services.youtube.appName'));

        $this->client->setDeveloperKey(config('services.youtube.token'));

        $this->client->setAccessType("offline");

        $this->service = new YouTube($this->client);
    }

    public function getGoogleAuth(){
        $authUrl = $this->client->createAuthUrl($this->scope);

        return redirect($authUrl);
    }

    public function authenticateUser(Request $request){
        $code = $request->code;

        $tokens = $this->client->fetchAccessTokenWithAuthCode($code);

        $this->storeTokens($tokens);
    }


    private function refreshToken(){
        if(Cache::has('access_token') && !$this->client->isAccessTokenExpired()){
            return Cache::get('access_token');
        }

        if($this->client->isAccessTokenExpired()){
            $refreshToken = GoogleToken::all()->last();

            $tokens = collect($this->client->refreshToken($refreshToken->token));

            $token = $this->storeTokens($tokens);
        }

        return $token;
    }

    private function storeTokens($tokens){

        $collection = json_decode(collect($tokens)->toJson(), true);

        // TODO store access token in cache
        Cache::put("access_token", $collection["access_token"], 1800);

        // check if token exists in database
        if(!$token = GoogleToken::where("token", $collection['refresh_token'])->first()){
            GoogleToken::create([
                "token" => $collection['refresh_token'],
            ]);
        }

        return Cache::get("access_token");
    }

    public function listYoutubeChannels(){
        $token = $this->refreshToken();
    }

    private function listVideos(String $id){
        $token = $this->refreshToken();

        $response = $this->service->videos->listVideos('player', ["id" => $id]);

        return substr(explode(" ", $response->items[0]['player']['embedHtml'])[3], 7);
    }

    private function uploadVideo(Array $request){
        $this->refreshToken();

        try{
            $snippet = new VideoSnippet();

            $snippet->setTitle($request["title"]);
            $snippet->setDescription($request["description"]);
            $snippet->setTags($request["tags"]);
            //$snippet->setCategory(28);

            $status = new VideoStatus();
            $status->privacyStatus = "unlisted";

            $video = new Video();

            $video->setSnippet($snippet);
            $video->setStatus($status);

            $chunkSize = 1 * 1024 * 1024;

            $this->client->setDefer(true);

            $insert = $this->service->videos->insert('status,snippet', $video);

            $media = new MediaFileUpload(
                $this->client,
                $insert,
                "video/*",
                null,
                true,
                $chunkSize
            );

            $media->setFileSize(filesize($request["lessonVideo"]));

            // Read the file and upload in chunks
            $status = false;
            $handle = fopen($request["lessonVideo"], 'rb');

            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSize);
                $status = $media->nextChunk($chunk);
            }

            fclose($handle);

            $this->client->setDefer(false);

            $thumbnail = $this->uploadThumbnail($request["lessonThumbnail"], $status['id']);

            return [
                "videoLink" => $this->listVideos($status['id']),
                "videoId" => $status['id'],
                "thumbnail" => $thumbnail,
            ];

         }  catch (\Google\Service\Exception $e) {
            throw new \Exception($e->getMessage());
        } catch (\Google\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateVideo(Request $request){
        $this->refreshToken();

        try {
            $snippet = new VideoSnippet();

            $snippet->setTitle($request->title);
            $snippet->setDescription($request->description);
            $snippet->setTags($request->tags);
            //$snippet->setCategory(28);

            $status = new VideoStatus();
            $status->privacyStatus = "unlisted";
            $video = new Video();

            $video->setSnippet($snippet);
            $video->setStatus($status);
            $video->setId($request->videoId);

            $response = $this->service->videos->update('status,snippet', $video);

            return [
                "videoId" => $response['id'],
            ];

        }  catch (\Google_Service_Exception $e) {
            throw new \Exception($e->getMessage());
        } catch (\Google_Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteVideo(String $id){
        $this->refreshToken();

        return $this->service->videos->delete($id);
    }

    public function uploadThumbnail($file, $videoId){
        try{
            $chunkSizeBytes = 1 * 1024 * 1024;

            $this->client->setDefer(true);

            $fileRequest = $this->service->thumbnails->set($videoId);

            $media = new MediaFileUpload(
                $this->client,
                $fileRequest,
                "image/png",
                null,
                true,
                $chunkSizeBytes,
            );

            $media->setFileSize(filesize($file));

            $status = false;
            $handle = fopen($file, 'rb');

            while(!$status && !feof($handle)){
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }

            fclose($handle);

            $this->client->setDefer(false);

            return $status['items'][0]['default']['url'];
        } catch (\Google_Service_Exception $e) {
            throw new \Exception($e->getMessage());
        } catch (\Google_Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function createPlaylist($title){
        $this->refreshToken();

        try{
        $path = "id,snippet,status";

        $snippet = new PlaylistSnippet();

        $snippet->setTitle($title);
        $track = strtolower($title);
        $snippet->setDescription("A collection of the videos for the {$track} track on the ADA LMS");

        $status = new PlaylistStatus();
        $status->setPrivacyStatus("unlisted");

        $playlist = new Playlist();
        $playlist->setStatus($status);
        $playlist->setSnippet($snippet);

        $response = $this->service->playlists->insert($path, $playlist);

        return $response["id"];
        }catch (\Google_Service_Exception $e) {
            throw new \Exception($e->getMessage());
        } catch (\Google_Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function uploadVideoToPlaylist(Array $request): array
    {
        $this->refreshToken();

        try{
            // upload vide and get video id
            $videoDetails = $this->uploadVideo($request);

            // setup params for playlist
            $course = Course::whereTitle($request["courseTitle"])->first();
            $playlistId = $course->playlistId;

            // setup resource
            $resource = new ResourceId();
            $resource->setKind("youtube#video");
            $resource->setVideoId($videoDetails["videoId"]);

            $parts = "id,snippet";
            $snippet = new PlaylistItemSnippet();
            $snippet->setPlaylistId($playlistId);
            $snippet->setResourceId($resource);
            $item = new PlaylistItem();
            $item->setSnippet($snippet);

            $response = $this->service->playlistItems->insert($parts, $item);

            return [
                "videoLink" => $this->listVideos($videoDetails["videoId"]),
                "thumbnail" => $response["snippet"]["thumbnails"]["default"]["url"],
                "youtube_video_id" => $videoDetails["videoId"],
            ];
        } catch (\Google_Service_Exception $e) {
            throw new \Exception($e->getMessage());
        } catch (\Google_Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getVideosViews(){
        $this->refreshToken();

        try{
            $reportData = new ReportData([
                "endDate" => today()->addWeek()->format("Y-m-d"),
                "startDate" => today()->format("Y-m-d"),
                "ids" => "channel==UCtaa9WH19QmP2sIkqQXXDgw",
                "metrics" => "views"
            ]);
            $report = new Report();

            $report->setData($reportData);;

            $response = $this->service->analytics->list($report);

            return $response;
        }
        catch (\Google_Service_Exception $e) {
            throw new \Exception($e->getMessage());
        } catch (\Google_Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }
}

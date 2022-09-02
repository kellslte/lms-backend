<?php
namespace App\Services;

use Google\Client;
use App\Models\YouTubeToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Google\Service\YouTube\VideoSnippet;
use Google\Service\YouTube\VideoStatus;
use Google\Service\YouTube\Video;
use Google\Http\MediaFileUpload;

class YoutubeService {
    protected $client;

    protected $scope = [
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/youtube.upload',
        'https://www.googleapis.com/auth/youtube.readonly'
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

        $this->service = new \Google_Service_YouTube($this->client);
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
            $refreshToken = YouTubeToken::all()->last();

            $tokens = collect($this->client->refreshToken($refreshToken->token));

            $token = $this->storeTokens($tokens);
        }

        return $token;
    }

    private function storeTokens($tokens){

        $collection = json_decode(collect($tokens)->toJson(), true);

        // TODO store access token in cache
        Cache::put("access_token", $collection["access_token"], 1800);

        // TODO store refresh token in database
        YouTubeToken::create([
            "token" => $collection['refresh_token'],
        ]);

        return Cache::get("access_token");
    }

    public function listYoutubeChannels(){
        $token = $this->refreshToken();
    }

    public function listVideos(String $id){
        $token = $this->refreshToken();

        $response = $this->service->videos->listVideos('player', ["id" => $id]);

        return substr(explode(" ", $response->items[0]['player']['embedHtml'])[3], 7);
    }

    public function uploadVideo(Request $request){
        $this->refreshToken();

        try{
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

            $media->setFileSize(filesize($request->file('lessonVideo')));

            // Read the file and upload in chunks
            $status = false;
            $handle = fopen($request->file('lessonVideo'), 'rb');

            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSize);
                $status = $media->nextChunk($chunk);
            }

            fclose($handle);

            $this->client->setDefer(false);

            $thumbnail = $this->uploadThumbnail($request->file('lessonThumbnail'), $status['id']);
            
            return [
                "videoLink" => $this->listVideos($status['id']),
                "videoId" => $status['id'],
                "thumbnail" => $thumbnail,
            ];

         }  catch (\Google_Service_Exception $e) {
            throw new \Exception($e->getMessage());
        } catch (\Google_Exception $e) {
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
            $status->privacyStatus = "private";

            $video = new Video();

            $video->setSnippet($snippet);
            $video->setStatus($status);
            $video->setId($request->videoId);

            $response = $this->service->videos->update('status,snippet', $video);

            return [
                "videoId" => $response['id'],
                "snippet" => $response['snippet'],
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
}
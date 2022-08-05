<?php
namespace App\Services;

use App\Mail\SendMagicLinkToUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class MagicLinkService {

    public function __construct($user) {
        $this->sendLoginLinkToUser($this->createToken($user));
    }
    
    protected function createToken($user){
        $plaintext = Str::random(32);

        $expiresAt = now()->addMinutes(15);
        
        $user->tokens()->create([
            'token' => hash('sha256', $plaintext),
            'expires_at' => $expiresAt,
        ]);

        return [
            'token' => $plaintext,
            'expires_at' => $expiresAt,
            'user' => $user
        ];
    }

    protected function sendLoginLinkToUser(array $data){
        Mail::to($data['user']->email)->queue(new SendMagicLinkToUser($data['token'], $data['expires_at'],  $data['user']));
    }
}
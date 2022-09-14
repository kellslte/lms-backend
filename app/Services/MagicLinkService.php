<?php
namespace App\Services;

use App\Mail\SendMagicLinkToUser;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class MagicLinkService {
    public static function createToken($user){
        $plaintext = Str::random(32);

        $expiresAt = now()->addDays(2);
        
        $user->magictokens()->create([
            'token' => hash('sha256', $plaintext),
            'expires_at' => $expiresAt,
        ]);

        return [
            'token' => $plaintext,
            'expires_at' => $expiresAt
        ];
    }
}
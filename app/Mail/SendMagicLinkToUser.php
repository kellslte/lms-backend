<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMagicLinkToUser extends

Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $token, $expiresAt, $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $expiresAt, $user)
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.send-magic-link', [
            'url' => config('app.front.url').URL::temporarySignedRoute('verify-login', $this->expiresAt, [
                'token' => $this->token,
            ], false),
            'user' => $this->user,
        ])->subject('Your Login Link');
    }
}

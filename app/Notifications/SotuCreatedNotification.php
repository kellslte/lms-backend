<?php

namespace App\Notifications;

use App\Models\Sotu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SotuCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public Sotu $sotu){}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Hey there, a new SOTU meeting has been fixed')
                    ->line('Go to your dashboard to check it out')
                    ->action('Go to your dashboard', url(config('app.front.url')))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            "type" => "sotu_fixed",
            "time" => time(),
            "message" => "A new sotu meeting has been fixed"
        ];
    }
}

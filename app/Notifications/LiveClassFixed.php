<?php

namespace App\Notifications;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LiveClassFixed extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ($notifiable->text_message_preference) ? 'sms': ['mail', 'database'];
    }

    public function toSms($notifiable){
        return SmsService::send("Hi, {$notifiable->name}, a live class has been fixed! Go to your dashboard to check the details of the class", $notifiable->phonenumber, "The ADA Team");
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('ADA Software Engineering Internship')
                    ->line("Hi, {$notifiable->name},")
                    ->line("A Live class has been fixed! Go to your dashboard to check out the details of the class")
                    ->action('Go to dashboard', url('/'))
                    ->line('Happy learning!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            "type" => "class_fixed",
            "time" => time(),
            "message" => "A live class has been fixed"
        ];;
    }
}

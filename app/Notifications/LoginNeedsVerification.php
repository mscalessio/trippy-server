<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class LoginNeedsVerification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            TwilioChannel::class,
        ];
    }

    public function toTwilio(object $notifiable): TwilioSmsMessage
    {
        return (new TwilioSmsMessage())
            ->content('Your Trippy login code is: ' . $this->generateLoginCode($notifiable));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    private function generateLoginCode(object $notifiable): int
    {
        $loginCode = rand(111111, 999999);

        $notifiable->update([
            'login_code' => $loginCode,
        ]);

        return $loginCode;
    }
}

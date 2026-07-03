<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EndpointDownAlert extends Notification
{
    use Queueable;

    public ?int $statusCode;
    public ?string $errorMessage;

    public function __construct(?int $statusCode, ?string $errorMessage)
    {
        $this->statusCode = $statusCode;
        $this->errorMessage = $errorMessage;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Endpoint Down: {$notifiable->name}")
            ->line("Endpoint **{$notifiable->name}** is down.")
            ->line("URL: {$notifiable->url}")
            ->when($this->statusCode, fn($m) => $m->line("Status Code: {$this->statusCode}"))
            ->when($this->errorMessage, fn($m) => $m->line("Error: {$this->errorMessage}"))
            ->action('View Dashboard', url('/dashboard'))
            ->line('This is an automated alert from PulseCheck.');
    }
}

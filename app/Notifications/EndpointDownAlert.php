<?php

namespace App\Notifications;

use App\Models\Endpoint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EndpointDownAlert extends Notification
{
    use Queueable;

    public Endpoint $endpoint;
    public ?int $statusCode;
    public ?string $errorMessage;

    public function __construct(Endpoint $endpoint, ?int $statusCode, ?string $errorMessage)
    {
        $this->endpoint = $endpoint;
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
            ->subject("Endpoint Down: {$this->endpoint->name}")
            ->line("Endpoint **{$this->endpoint->name}** is down.")
            ->line("URL: {$this->endpoint->url}")
            ->when($this->statusCode, fn($m) => $m->line("Status Code: {$this->statusCode}"))
            ->when($this->errorMessage, fn($m) => $m->line("Error: {$this->errorMessage}"))
            ->action('View Dashboard', url('/dashboard'))
            ->line('This is an automated alert from PulseCheck.');
    }
}

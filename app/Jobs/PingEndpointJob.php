<?php

namespace App\Jobs;

use App\Models\Endpoint;
use App\Models\PingLog;
use App\Notifications\EndpointDownAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PingEndpointJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Endpoint $endpoint;

    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function handle()
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->endpoint->timeout)
                ->withOptions(['allow_redirects' => true])
                ->send($this->endpoint->method, $this->endpoint->url);

            $responseTimeMs = (int) ((microtime(true) - $startTime) * 1000);
            $statusCode = $response->status();
            $isSuccess = $statusCode === $this->endpoint->expected_status_code;
            $errorMessage = null;
        } catch (\Exception $e) {
            $responseTimeMs = (int) ((microtime(true) - $startTime) * 1000);
            $statusCode = null;
            $isSuccess = false;
            $errorMessage = $e->getMessage();
        }

        PingLog::create([
            'endpoint_id' => $this->endpoint->id,
            'status_code' => $statusCode,
            'response_time_ms' => $responseTimeMs,
            'is_success' => $isSuccess,
            'error_message' => $errorMessage,
            'pinged_at' => now(),
        ]);

        $this->endpoint->update(['last_pinged_at' => now()]);

        if (!$isSuccess) {
            try {
                $this->endpoint->notify(new EndpointDownAlert($statusCode, $errorMessage));
            } catch (\Exception $e) {
                // notification failed silently
            }
        }
    }
}

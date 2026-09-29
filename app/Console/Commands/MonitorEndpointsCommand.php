<?php

namespace App\Console\Commands;

use App\Jobs\PingEndpointJob;
use App\Models\Endpoint;
use Illuminate\Console\Command;

class MonitorEndpointsCommand extends Command
{
    protected $signature = 'pulse:check';
    protected $description = 'Ping all active endpoints and log results';

    public function handle()
    {
        $endpoints = Endpoint::active()->get()->filter(
            fn (Endpoint $endpoint) => is_null($endpoint->last_pinged_at)
                || $endpoint->last_pinged_at->lte(now()->subMinutes($endpoint->interval_minutes))
        );

        if ($endpoints->isEmpty()) {
            $this->info('No active endpoints found.');
            return 0;
        }

        $this->info("Dispatching ping jobs for {$endpoints->count()} endpoint(s)...");

        foreach ($endpoints as $endpoint) {
            PingEndpointJob::dispatch($endpoint);
            $this->line("  Dispatched: {$endpoint->name} ({$endpoint->url})");
        }

        $this->info('All ping jobs dispatched.');
        return 0;
    }
}

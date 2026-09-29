<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $endpoints = Endpoint::with('latestPingLog')->orderBy('name')->get();

        $total = $endpoints->count();
        $up = $endpoints->filter(fn ($e) => $e->latestPingLog && $e->latestPingLog->is_success)->count();
        $down = $endpoints->filter(fn ($e) => $e->latestPingLog && !$e->latestPingLog->is_success)->count();
        $pending = $total - $up - $down;

        $avgResponseTime = $endpoints
            ->filter(fn ($e) => $e->latestPingLog && $e->latestPingLog->response_time_ms)
            ->pluck('latestPingLog.response_time_ms')
            ->avg();

        return view('dashboard', compact('endpoints', 'total', 'up', 'down', 'pending', 'avgResponseTime'));
    }
}

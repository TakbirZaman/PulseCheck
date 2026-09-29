<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use Illuminate\Http\Request;

class EndpointController extends Controller
{
    public function index()
    {
        $endpoints = Endpoint::with('latestPingLog')->orderBy('name')->get();
        return view('endpoints.index', compact('endpoints'));
    }

    public function create()
    {
        return view('endpoints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:255'],
            'method' => ['required', 'in:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS'],
            'expected_status_code' => ['required', 'integer', 'min:100', 'max:599'],
            'timeout' => ['required', 'integer', 'min:1', 'max:300'],
            'interval_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        Endpoint::create($validated);

        return redirect(route('endpoints.index'))->with('success', 'Endpoint created successfully.');
    }

    public function show(Endpoint $endpoint)
    {
        $endpoint->load('latestPingLog');
        $pingLogs = $endpoint->pingLogs()->orderByDesc('pinged_at')->paginate(20);

        $latest = $endpoint->latestPingLog;
        $isUp = $latest && $latest->is_success;

        $recentLogs = $endpoint->pingLogs()->orderByDesc('pinged_at')->limit(30)->get()->reverse()->values();
        $avgResponseTime = $recentLogs->where('is_success', true)->pluck('response_time_ms')->avg();
        $uptimePercentage = $recentLogs->isNotEmpty()
            ? round($recentLogs->where('is_success', true)->count() / $recentLogs->count() * 100, 1)
            : 0;

        return view('endpoints.show', compact('endpoint', 'pingLogs', 'latest', 'isUp', 'avgResponseTime', 'uptimePercentage', 'recentLogs'));
    }

    public function edit(Endpoint $endpoint)
    {
        return view('endpoints.edit', compact('endpoint'));
    }

    public function update(Request $request, Endpoint $endpoint)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:255'],
            'method' => ['required', 'in:GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS'],
            'expected_status_code' => ['required', 'integer', 'min:100', 'max:599'],
            'timeout' => ['required', 'integer', 'min:1', 'max:300'],
            'interval_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $endpoint->update($validated);

        return redirect(route('endpoints.index'))->with('success', 'Endpoint updated successfully.');
    }

    public function destroy(Endpoint $endpoint)
    {
        $endpoint->delete();
        return redirect(route('endpoints.index'))->with('success', 'Endpoint deleted successfully.');
    }

    public function toggle(Endpoint $endpoint)
    {
        $endpoint->update(['is_active' => !$endpoint->is_active]);
        $status = $endpoint->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Endpoint {$status} successfully.");
    }
}

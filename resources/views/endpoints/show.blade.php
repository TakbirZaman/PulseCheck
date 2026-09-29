@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <a href="{{ route('endpoints.index') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Endpoints
        </a>
    </div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $endpoint->name }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $isUp ? 'bg-green-100 text-green-800' : ($latest ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-500') }}">
                    {{ $isUp ? 'UP' : ($latest ? 'DOWN' : 'PENDING') }}
                </span>
                @unless($endpoint->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Disabled
                    </span>
                @endunless
            </div>
            <p class="text-sm text-gray-500 mt-1">{{ $endpoint->url }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('endpoints.edit', $endpoint) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                Edit
            </a>
            <form method="POST" action="{{ route('endpoints.toggle', $endpoint) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                        {{ $endpoint->is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                    {{ $endpoint->is_active ? 'Disable' : 'Enable' }}
                </button>
            </form>
            <form method="POST" action="{{ route('endpoints.destroy', $endpoint) }}"
                  x-data
                  x-on:submit="event.preventDefault(); if(confirm('Are you sure you want to delete this endpoint? This action cannot be undone.')) { $el.submit(); }">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Avg Response Time</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $avgResponseTime ? round($avgResponseTime) . 'ms' : 'N/A' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Uptime (Last 30)</p>
            <p class="text-2xl font-bold {{ $uptimePercentage >= 99 ? 'text-green-600' : ($uptimePercentage >= 95 ? 'text-yellow-600' : 'text-red-600') }} mt-1">
                {{ $uptimePercentage }}%
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Total Checks</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $endpoint->pingLogs()->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Configuration</h2>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-6 text-sm">
            <div>
                <p class="text-gray-500">Method</p>
                <p class="font-medium text-gray-900 mt-1">{{ $endpoint->method }}</p>
            </div>
            <div>
                <p class="text-gray-500">Expected Status</p>
                <p class="font-medium text-gray-900 mt-1">{{ $endpoint->expected_status_code }}</p>
            </div>
            <div>
                <p class="text-gray-500">Timeout</p>
                <p class="font-medium text-gray-900 mt-1">{{ $endpoint->timeout }}s</p>
            </div>
            <div>
                <p class="text-gray-500">Check Interval</p>
                <p class="font-medium text-gray-900 mt-1">{{ $endpoint->interval_minutes }} min</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Ping History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pingLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-gray-900">{{ $log->pinged_at->format('M j, g:i:s A') }}</td>
                            <td class="px-5 py-3 text-gray-900">{{ $log->status_code ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-gray-900">{{ $log->response_time_ms }}ms</td>
                            <td class="px-5 py-3">
                                @if($log->is_success)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Success</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-500 max-w-xs truncate">{{ $log->error_message ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-gray-400">No ping logs yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pingLogs->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $pingLogs->links() }}
            </div>
        @endif
    </div>
@endsection

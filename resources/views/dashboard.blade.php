@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Overview of all monitored endpoints</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Endpoints</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $total }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Up</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $up }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Down</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $down }}</p>
                </div>
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-gray-400 mt-1">{{ $pending }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Avg Response</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $avgResponseTime ? round($avgResponseTime) . 'ms' : 'N/A' }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Endpoints</h2>
        <a href="{{ route('endpoints.create') }}"
           class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Endpoint
        </a>
    </div>

    @forelse($endpoints as $endpoint)
        @php
            $latest = $endpoint->latestPingLog;
            $isUp = $latest && $latest->is_success;
        @endphp
        <a href="{{ route('endpoints.show', $endpoint) }}"
           class="block bg-white rounded-xl border border-gray-200 p-5 mb-3 hover:shadow-md transition-shadow border-l-4 {{ $isUp ? 'border-l-green-500' : ($latest ? 'border-l-red-500' : 'border-l-gray-300') }}">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <h3 class="text-base font-semibold text-gray-900 truncate">{{ $endpoint->name }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $isUp ? 'bg-green-100 text-green-800' : ($latest ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-500') }}">
                            {{ $isUp ? 'UP' : ($latest ? 'DOWN' : 'PENDING') }}
                        </span>
                        @unless($endpoint->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Disabled
                            </span>
                        @endunless
                    </div>
                    <p class="text-sm text-gray-500 mt-1 truncate">{{ $endpoint->url }}</p>
                </div>
                <div class="flex items-center gap-6 text-sm text-gray-500 ml-4 flex-shrink-0">
                    @if($latest)
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">{{ $latest->status_code ?? 'N/A' }}</p>
                            <p class="text-xs">Status</p>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">{{ $latest->response_time_ms }}ms</p>
                            <p class="text-xs">Response</p>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-gray-900">{{ $latest->pinged_at->diffForHumans() }}</p>
                            <p class="text-xs">Last Ping</p>
                        </div>
                    @else
                        <p class="text-gray-400">No data yet</p>
                    @endif
                </div>
            </div>
            @if($latest && !$isUp && $latest->error_message)
                <div class="mt-3 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2">
                    {{ $latest->error_message }}
                </div>
            @endif
        </a>
    @empty
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No endpoints yet</h3>
            <p class="text-sm text-gray-500 mb-4">Get started by adding your first endpoint to monitor.</p>
            <a href="{{ route('endpoints.create') }}"
               class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Your First Endpoint
            </a>
        </div>
    @endforelse
@endsection

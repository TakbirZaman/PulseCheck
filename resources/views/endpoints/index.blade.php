@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Endpoints</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your monitored endpoints</p>
        </div>
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
        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-3 {{ $endpoint->is_active ? '' : 'opacity-60' }}">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('endpoints.show', $endpoint) }}" class="text-base font-semibold text-gray-900 hover:text-indigo-600 transition-colors truncate">
                            {{ $endpoint->name }}
                        </a>
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
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                        <span>{{ $endpoint->method }}</span>
                        <span>Expected: {{ $endpoint->expected_status_code }}</span>
                        <span>Timeout: {{ $endpoint->timeout }}s</span>
                        @if($latest)
                            <span>{{ $latest->response_time_ms }}ms</span>
                            <span>{{ $latest->pinged_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                    <form method="POST" action="{{ route('endpoints.toggle', $endpoint) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors
                                {{ $endpoint->is_active ? 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}"
                                title="{{ $endpoint->is_active ? 'Disable' : 'Enable' }}">
                            {{ $endpoint->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    <a href="{{ route('endpoints.edit', $endpoint) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 hover:bg-gray-100 transition-colors">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('endpoints.destroy', $endpoint) }}"
                          x-data
                          x-on:submit="event.preventDefault(); if(confirm('Are you sure you want to delete this endpoint? This action cannot be undone.')) { $el.submit(); }">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No endpoints yet</h3>
            <p class="text-sm text-gray-500 mb-4">Add your first endpoint to start monitoring.</p>
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

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

    <div class="max-w-2xl">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Endpoint</h1>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('endpoints.update', $endpoint) }}" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $endpoint->name) }}" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                <input id="url" type="url" name="url" value="{{ old('url', $endpoint->url) }}" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="method" class="block text-sm font-medium text-gray-700 mb-1">Method</label>
                    <select id="method" name="method"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @foreach(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'] as $method)
                            <option value="{{ $method }}" {{ old('method', $endpoint->method) === $method ? 'selected' : '' }}>{{ $method }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="expected_status_code" class="block text-sm font-medium text-gray-700 mb-1">Expected Status</label>
                    <input id="expected_status_code" type="number" name="expected_status_code" value="{{ old('expected_status_code', $endpoint->expected_status_code) }}" min="100" max="599"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="timeout" class="block text-sm font-medium text-gray-700 mb-1">Timeout (seconds)</label>
                    <input id="timeout" type="number" name="timeout" value="{{ old('timeout', $endpoint->timeout) }}" min="1" max="300"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label for="interval_minutes" class="block text-sm font-medium text-gray-700 mb-1">Check Interval (min)</label>
                    <input id="interval_minutes" type="number" name="interval_minutes" value="{{ old('interval_minutes', $endpoint->interval_minutes) }}" min="1" max="1440"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input id="is_active" type="checkbox" name="is_active" value="1" {{ old('is_active', $endpoint->is_active) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm text-gray-700">Active</label>
            </div>

            <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Update Endpoint
                </button>
                <a href="{{ route('endpoints.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            </div>
        </form>
    </div>
@endsection

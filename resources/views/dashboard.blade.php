<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PulseCheck Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800">PulseCheck</h1>
            <span class="text-sm text-gray-500">{{ now()->format('M j, Y g:i A') }}</span>
        </div>

        <div class="grid gap-4">
            @forelse($endpoints as $endpoint)
                @php
                    $latest = $endpoint->latestPingLog;
                    $isUp = $latest && $latest->is_success;
                @endphp
                <div class="bg-white rounded-lg shadow p-6 border-l-4 {{ $isUp ? 'border-green-500' : 'border-red-500' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">{{ $endpoint->name }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ $endpoint->url }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $isUp ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $isUp ? 'UP' : 'DOWN' }}
                            </span>
                        </div>
                    </div>

                    @if($latest)
                        <div class="mt-4 grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Status:</span>
                                <span class="ml-1 font-medium">{{ $latest->status_code ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Response:</span>
                                <span class="ml-1 font-medium">{{ $latest->response_time_ms }}ms</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Pinged:</span>
                                <span class="ml-1 font-medium">{{ $latest->pinged_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @if(!$isUp && $latest->error_message)
                            <div class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded">
                                {{ $latest->error_message }}
                            </div>
                        @endif
                    @else
                        <div class="mt-4 text-sm text-gray-400">Not yet pinged.</div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                    No endpoints configured. Run <code class="bg-gray-100 px-2 py-1 rounded">php artisan db:seed</code> to add sample endpoints.
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>

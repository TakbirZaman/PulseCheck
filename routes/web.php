<?php

use App\Models\Endpoint;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    $endpoints = Endpoint::with('latestPingLog')->orderBy('name')->get();
    return view('dashboard', compact('endpoints'));
});

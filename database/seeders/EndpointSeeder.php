<?php

namespace Database\Seeders;

use App\Models\Endpoint;
use Illuminate\Database\Seeder;

class EndpointSeeder extends Seeder
{
    public function run()
    {
        Endpoint::create([
            'name' => 'Google',
            'url' => 'https://www.google.com',
            'method' => 'GET',
            'expected_status_code' => 200,
            'timeout' => 10,
            'interval_minutes' => 5,
            'is_active' => true,
        ]);

        Endpoint::create([
            'name' => 'GitHub',
            'url' => 'https://www.github.com',
            'method' => 'GET',
            'expected_status_code' => 200,
            'timeout' => 10,
            'interval_minutes' => 5,
            'is_active' => true,
        ]);

        Endpoint::create([
            'name' => 'JSONPlaceholder',
            'url' => 'https://jsonplaceholder.typicode.com/posts/1',
            'method' => 'GET',
            'expected_status_code' => 200,
            'timeout' => 10,
            'interval_minutes' => 5,
            'is_active' => true,
        ]);

        Endpoint::create([
            'name' => 'Example Site',
            'url' => 'https://example.com',
            'method' => 'GET',
            'expected_status_code' => 200,
            'timeout' => 10,
            'interval_minutes' => 5,
            'is_active' => true,
        ]);
    }
}

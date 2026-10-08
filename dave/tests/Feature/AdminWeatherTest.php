<?php

use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('displays mapped weather data on the admin dashboard', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::response([
            'current' => [
                'temperature_2m' => 29.4,
                'relative_humidity_2m' => 72,
                'wind_speed_10m' => 11.2,
                'weather_code' => 3,
            ],
        ]),
    ]);

    $admin = User::factory()->create([
        'role' => 'Admin',
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk()
        ->assertSee('Arakan, North Cotabato')
        ->assertSee('29.4°C')
        ->assertSee('Partly cloudy')
        ->assertSee('72%')
        ->assertSee('11.2 km/h');

    Http::assertSent(fn (Request $request) => str_starts_with($request->url(), config('services.weather.endpoint')));
});

it('keeps the admin dashboard usable when weather is unavailable', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::failedConnection(),
    ]);

    $admin = User::factory()->create([
        'role' => 'Admin',
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk()
        ->assertSee('Weather unavailable')
        ->assertSee('Weather information is temporarily unavailable.');
});

it('displays weather data on the staff dashboard', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::response([
            'current' => [
                'temperature_2m' => 26.1,
                'relative_humidity_2m' => 68,
                'wind_speed_10m' => 10.5,
                'weather_code' => 0,
            ],
        ]),
    ]);

    $staff = User::factory()->create([
        'role' => 'Staff',
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($staff)->get(route('staff.dashboard'));

    $response->assertOk()
        ->assertSee('Current Weather')
        ->assertSee('Arakan, North Cotabato')
        ->assertSee('26.1°C');
});

it('displays weather data on the customer dashboard', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::response([
            'current' => [
                'temperature_2m' => 31.7,
                'relative_humidity_2m' => 65,
                'wind_speed_10m' => 12.8,
                'weather_code' => 61,
            ],
        ]),
    ]);

    $customer = User::factory()->create([
        'role' => 'Customer',
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($customer)->get(route('customer.dashboard'));

    $response->assertOk()
        ->assertSee('Current Weather')
        ->assertSee('Arakan, North Cotabato')
        ->assertSee('31.7°C')
        ->assertSee('Rain');
});

it('falls back when the weather api returns an invalid response', function () {
    Http::fake([
        'api.open-meteo.com/*' => Http::response(['unexpected' => 'payload'], 503),
    ]);

    $admin = User::factory()->create([
        'role' => 'Admin',
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk()->assertSee('Weather information is temporarily unavailable.');
});

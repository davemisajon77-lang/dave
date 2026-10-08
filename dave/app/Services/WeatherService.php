<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * @return array{city: string, temperature: float|int, condition: string, humidity: float|int, wind_speed: float|int}|null
     */
    public function current(): ?array
    {
        try {
            $response = Http::connectTimeout(3)
                ->timeout(5)
                ->get(config('services.weather.endpoint'), [
                    'latitude' => config('services.weather.latitude'),
                    'longitude' => config('services.weather.longitude'),
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                    'temperature_unit' => config('services.weather.temperature_unit', 'celsius'),
                    'wind_speed_unit' => config('services.weather.wind_speed_unit', 'kmh'),
                    'timezone' => 'auto',
                ]);

            if (! $response->successful()) {
                Log::warning('Weather API returned an unsuccessful response.', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $payload = $response->json();
            $current = $payload['current'] ?? null;

            if (! is_array($current) || ! isset(
                $current['temperature_2m'],
                $current['relative_humidity_2m'],
                $current['wind_speed_10m'],
                $current['weather_code']
            )) {
                Log::warning('Weather API returned an invalid payload.');

                return null;
            }

            return [
                'city' => (string) config('services.weather.city'),
                'temperature' => $current['temperature_2m'],
                'condition' => $this->conditionFor((int) $current['weather_code']),
                'humidity' => $current['relative_humidity_2m'],
                'wind_speed' => $current['wind_speed_10m'],
            ];
        } catch (ConnectionException $exception) {
            Log::warning('Weather API connection failed.', ['message' => $exception->getMessage()]);

            return null;
        }
    }

    private function conditionFor(int $weatherCode): string
    {
        return match (true) {
            $weatherCode === 0 => 'Clear sky',
            in_array($weatherCode, [1, 2, 3], true) => 'Partly cloudy',
            in_array($weatherCode, [45, 48], true) => 'Foggy',
            in_array($weatherCode, [51, 53, 55, 56, 57], true) => 'Drizzle',
            in_array($weatherCode, [61, 63, 65, 66, 67], true) => 'Rain',
            in_array($weatherCode, [71, 73, 75, 77], true) => 'Snow',
            in_array($weatherCode, [80, 81, 82], true) => 'Rain showers',
            in_array($weatherCode, [85, 86], true) => 'Snow showers',
            in_array($weatherCode, [95, 96, 99], true) => 'Thunderstorm',
            default => 'Unavailable',
        };
    }
}

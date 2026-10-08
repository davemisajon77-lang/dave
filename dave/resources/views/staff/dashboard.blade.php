<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Welcome, Staff!</h3>
                    <p class="text-gray-600 mb-6">You can manage and update the status of patient appointments.</p>

                    <a href="{{ route('appointments.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-indigo-700">
                        View Appointments
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 md:col-span-3">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-wide text-gray-500">Current Weather</p>
                        @if ($weather)
                            <h3 class="mt-1 text-xl font-semibold text-gray-900">{{ $weather['city'] }}</h3>
                        @else
                            <h3 class="mt-1 text-xl font-semibold text-gray-900">Weather unavailable</h3>
                        @endif
                    </div>
                    @if ($weather)
                        <p class="text-3xl font-bold text-gray-900">{{ $weather['temperature'] }}°C</p>
                    @endif
                </div>

                @if ($weather)
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-sm text-gray-500">Condition</p>
                            <p class="mt-1 font-medium text-gray-900">{{ $weather['condition'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Humidity</p>
                            <p class="mt-1 font-medium text-gray-900">{{ $weather['humidity'] }}%</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Wind speed</p>
                            <p class="mt-1 font-medium text-gray-900">{{ $weather['wind_speed'] }} km/h</p>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-gray-600">Weather information is temporarily unavailable. Please try again later.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
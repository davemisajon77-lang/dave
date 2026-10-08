<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div class="bg-indigo-600 overflow-hidden shadow-sm sm:rounded-lg mb-6 text-white p-6">
                <h3 class="text-2xl font-bold text-white">Welcome, {{ Auth::user()->name }}!</h3>
                <p class="mt-2 text-white font-medium">Do you need a dental check-up or tooth cleaning? Book an appointment now.</p>
                <div class="mt-4">
                    <a href="{{ route('appointments.create') }}" class="bg-white text-indigo-700 px-4 py-2 rounded-md text-sm font-semibold hover:bg-gray-100 shadow">Book Appointment Now</a>
                </div>
            </div>

            <!-- Current Weather -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
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

            <!-- Recent Appointments Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Your Appointments</h3>
                        <a href="{{ route('appointments.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">See All &rarr;</a>
                    </div>

                    @php
                        $myAppointments = \App\Models\Appointment::where('user_id', Auth::id())->with('service')->latest()->take(5)->get();
                    @endphp

                    @if($myAppointments->count() > 0)
                        <div class="space-y-4">
                            @foreach($myAppointments as $appt)
                                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $appt->service->name }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $appt->status == 'Approved' ? 'bg-green-100 text-green-800' : ($appt->status == 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($appt->status == 'Completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                            {{ $appt->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">You haven't found or made an appointment yet. Book one above.!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
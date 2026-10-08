<?php

use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'Admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'Staff') {
        return redirect()->route('staff.dashboard');
    } elseif ($user->role === 'Customer') {
        return redirect()->route('customer.dashboard');
    }

    abort(403, 'Unauthorized action.');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Dashboard Route
Route::middleware(['auth', 'verified', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function (WeatherService $weatherService) {
        $staffCount = User::where('role', 'Staff')->count();
        $customerCount = User::where('role', 'Customer')->count();
        $adminCount = User::where('role', 'Admin')->count();
        $weather = $weatherService->current();

        return view('admin.dashboard', compact('staffCount', 'customerCount', 'adminCount', 'weather'));
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('services', ServiceController::class);
});

// Staff Dashboard Route
Route::middleware(['auth', 'verified', 'role:Staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', function (WeatherService $weatherService) {
        $weather = $weatherService->current();

        return view('staff.dashboard', compact('weather'));
    })->name('dashboard');
});

// Customer Dashboard Route
Route::middleware(['auth', 'verified', 'role:Customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', function (WeatherService $weatherService) {
        $weather = $weatherService->current();

        return view('customer.dashboard', compact('weather'));
    })->name('dashboard');
});

// Appointment Routes (Accessible by Authenticated Users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
});

require __DIR__.'/auth.php';

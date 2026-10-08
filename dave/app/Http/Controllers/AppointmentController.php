<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // Listahan ng mga appointment (Magkaiba depende sa Role)
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'Admin' || $user->role === 'Staff') {
            // Makikita ng Admin at Staff ang LAHAT ng appointment kasama ang service at doctor
            $appointments = Appointment::with(['user', 'service', 'doctor'])->latest()->get();
        } else {
            // Ang Customer ay sarili lang niyang appointment ang makikita
            $appointments = Appointment::where('user_id', $user->id)->with(['service', 'doctor'])->latest()->get();
        }

        return view('appointments.index', compact('appointments'));
    }

    // Form para makapag-book ang Customer
    public function create()
    {
        $services = Service::all();
        $doctors = Doctor::all(); // Idinagdag para lumabas ang mga doktor sa form

        return view('appointments.create', compact('services', 'doctors'));
    }

    // I-save ang bagong booking sa database
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'doctor_id' => ['required', 'exists:doctors,id'], // Idinagdag ang validation para sa doktor
            'appointment_date' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string'],
        ]);

        Appointment::create([
            'user_id' => Auth::id(),
            'service_id' => $request->service_id,
            'doctor_id' => $request->doctor_id, // Isinama ang pag-save ng doctor_id
            'appointment_date' => $request->appointment_date,
            'notes' => $request->notes,
            'status' => 'Pending', // Default status kapag bago pa lang nag-book
        ]);

        return redirect()->route('appointments.index')->with('success', 'Dental appointment booked successfully!');
    }

    // Form para i-edit ang appointment
    public function edit(Appointment $appointment)
    {
        $user = Auth::user();

        // Siguraduhing customer niya at Pending pa para pwede i-edit
        if ($user->role !== 'Customer' || $appointment->user_id !== $user->id || $appointment->status !== 'Pending') {
            return redirect()->route('appointments.index')->with('error', 'You cannot edit this appointment.');
        }

        $services = Service::all();
        $doctors = Doctor::all();

        return view('appointments.edit', compact('appointment', 'services', 'doctors'));
    }

    // I-update ang appointment sa database
    public function update(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        if ($user->role !== 'Customer' || $appointment->user_id !== $user->id || $appointment->status !== 'Pending') {
            return redirect()->route('appointments.index')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
        ]);

        $appointment->update([
            'service_id' => $request->service_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Dental appointment updated successfully!');
    }

    // Baguhin ang status (Para sa Admin/Staff: Approved, Completed, Cancelled)
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        if ($user->role !== 'Admin' && $user->role !== 'Staff') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => ['required', 'in:Pending,Approved,Completed,Cancelled'],
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment status updated successfully!');
    }

    // I-cancel ang sariling appointment ng Customer
    public function destroy(Appointment $appointment)
    {
        $user = Auth::user();

        // Pwede i-delete ng Admin o kung siya ang may-ari ng appointment at Pending pa
        if ($user->role === 'Admin' || ($appointment->user_id === $user->id && $appointment->status === 'Pending')) {
            $appointment->delete();

            return redirect()->route('appointments.index')->with('success', 'Appointment cancelled successfully.');
        }

        return redirect()->route('appointments.index')->with('error', 'You cannot cancel this appointment.');
    }
}

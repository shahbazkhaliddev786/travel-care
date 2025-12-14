<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display the schedule page with real appointment data
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('signin');
        }

        // Determine if user is a doctor or customer and fetch appropriate appointments
        if ($user->role === 'doctor') {
            $appointments = $this->getDoctorAppointments($user);
        } else {
            $appointments = $this->getCustomerAppointments($user);
        }

        // Group appointments by date and get calendar data
        $groupedAppointments = $this->groupAppointmentsByDate($appointments);
        $calendarData = $this->getCalendarData($appointments);

        return view('schedule', compact('groupedAppointments', 'calendarData', 'user'));
    }

    /**
     * Get appointments for a doctor (patients who booked with them)
     */
    private function getDoctorAppointments($user)
    {
        // Find the doctor record for this user
        $doctor = Doctor::where('user_id', $user->id)->first();
        
        if (!$doctor) {
            return collect();
        }

        return Transaction::where('doctor_id', $doctor->id)
            ->where('payment_status', 'completed')
            ->whereNotNull('appointment_date')
            ->where('appointment_date', '>=', Carbon::today())
            ->with(['user']) // Load customer information
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Get appointments for a customer (appointments they booked)
     */
    private function getCustomerAppointments($user)
    {
        return Transaction::where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->whereNotNull('appointment_date')
            ->where('appointment_date', '>=', Carbon::today())
            ->with(['doctor']) // Load doctor information
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Group appointments by date for display
     */
    private function groupAppointmentsByDate($appointments)
    {
        return $appointments->groupBy(function ($appointment) {
            return Carbon::parse($appointment->appointment_date)->format('Y-m-d');
        })->map(function ($dayAppointments, $date) {
            return [
                'date' => $date,
                'formatted_date' => Carbon::parse($date)->format('j F, Y'),
                'appointments' => $dayAppointments
            ];
        });
    }

    /**
     * Get calendar data with highlighted dates
     */
    private function getCalendarData($appointments)
    {
        $appointmentDates = $appointments->pluck('appointment_date')->map(function ($date) {
            return Carbon::parse($date)->day;
        })->unique()->values()->toArray();

        return [
            'highlighted_days' => $appointmentDates,
            'current_month' => Carbon::now()->format('F'),
            'current_year' => Carbon::now()->year
        ];
    }

    /**
     * Get appointment details for modal (AJAX endpoint)
     */
    public function getAppointmentDetails(Request $request, $transactionId)
    {
        $user = Auth::user();
        
        $transaction = Transaction::where('transaction_id', $transactionId)
            ->where(function ($query) use ($user) {
                if ($user->role === 'doctor') {
                    // For doctors, find appointments where they are the doctor
                    $doctor = Doctor::where('user_id', $user->id)->first();
                    if ($doctor) {
                        $query->where('doctor_id', $doctor->id);
                    }
                } else {
                    // For customers, find their own appointments
                    $query->where('user_id', $user->id);
                }
            })
            ->with(['user', 'doctor'])
            ->first();

        if (!$transaction) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        return response()->json([
            'transaction_id' => $transaction->transaction_id,
            'patient_name' => $transaction->user->name ?? 'Unknown',
            'patient_age' => $transaction->user->age ?? 'N/A',
            'doctor_name' => $transaction->doctor_name,
            'service_type' => $transaction->service_type,
            'appointment_date' => Carbon::parse($transaction->appointment_date)->format('j F, Y'),
            'appointment_time' => $transaction->appointment_time,
            'location' => $transaction->location ?? 'Remote Consultation',
            'amount' => $transaction->formatted_amount,
            'status' => $transaction->payment_status
        ]);
    }
}

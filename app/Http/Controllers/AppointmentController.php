<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function show(Doctor $doctor)
    {
        // Load doctor with relationships
        $doctor->load(['reviews']);
        
        // Generate available time slots based on working hours
        $availableDates = $this->generateAvailableDates();
        $timeSlots = $this->generateTimeSlots($doctor);
        
        return view('appointment', compact('doctor', 'availableDates', 'timeSlots'));
    }
    
    private function generateAvailableDates()
    {
        $dates = [];
        $startDate = Carbon::today();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayName = $i === 0 ? 'Today' : $date->format('D, d.m');
            
            $dates[] = [
                'day' => $dayName,
                'date' => $date->format('Y-m-d'),
                'available' => rand(2, 8) // Mock available slots for now
            ];
        }
        
        return $dates;
    }
    
    private function generateTimeSlots(Doctor $doctor)
    {
        $slots = [];
        
        // Get working hours (default 8:00 AM - 7:00 PM if not set)
        $startTime = $doctor->working_hours_from ?? '08:00:00';
        $endTime = $doctor->working_hours_to ?? '19:00:00';
        
        // Convert to Carbon instances - handle both H:i:s and H:i formats
        try {
            $start = Carbon::createFromFormat('H:i:s', $startTime);
        } catch (\Exception $e) {
            $start = Carbon::createFromFormat('H:i', $startTime);
        }
        
        try {
            $end = Carbon::createFromFormat('H:i:s', $endTime);
        } catch (\Exception $e) {
            $end = Carbon::createFromFormat('H:i', $endTime);
        }
        
        // Generate 30-minute slots
        while ($start < $end) {
            $slots[] = [
                'time' => $start->format('g:i A'),
                'value' => $start->format('H:i'),
                'available' => true // We can add logic here to check existing appointments
            ];
            $start->addMinutes(30);
        }
        
        return $slots;
    }
} 
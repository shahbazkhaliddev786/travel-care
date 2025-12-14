<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicProfileController extends Controller
{
    /**
     * Display the specified user's public profile.
     */
    public function show($userId)
    {
        try {
            // Find the user with related data
            $user = User::with(['customerProfile', 'doctor'])->findOrFail($userId);
            
            // Get doctor profile if user is a doctor
            $doctorProfile = $user->doctor;
            
            // Prepare user data for the view
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'country_code' => $user->country_code,
                'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('assets/icons/default-avatar.svg'),
                'role' => $user->role,
                'created_at' => $user->created_at,
                'age' => $user->customerProfile->age ?? null,
                'weight' => $user->customerProfile->weight ?? null,
                'biological_sex' => $user->customerProfile->biological_sex ?? null,
                'city' => $user->customerProfile->city ?? null,
                'country' => $user->customerProfile->country ?? null,
            ];
            
            // Add doctor-specific data if available
            if ($doctorProfile) {
                $userData['doctor'] = [
                    'specialization' => $doctorProfile->specialization,
                    'experience_years' => $doctorProfile->experience_years,
                    'consultation_fee' => $doctorProfile->consultation_fee,
                    // Keep 'bio' key for compatibility but source from 'description'
                    'bio' => $doctorProfile->description,
                    'qualifications' => $doctorProfile->qualifications,
                    'languages' => $doctorProfile->languages,
                    'working_hours' => $doctorProfile->working_hours,
                    'hospital_name' => $doctorProfile->hospital_name,
                    'hospital_address' => $doctorProfile->hospital_address,
                ];
            }
            
            return view('profile.public-profile', compact('userData', 'user'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'User not found or profile unavailable.');
        }
    }
}
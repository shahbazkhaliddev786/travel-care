<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Doctor;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $email = $googleUser->getEmail();
            $name = $googleUser->getName() ?: (explode('@', $email)[0] ?? 'Doctor');

            // Find existing user by email
            $user = User::where('email', $email)->first();

            if ($user) {
                // If user is not a doctor yet, create a doctor profile and switch role
                if (!$user->isDoctor()) {
                    // Create doctor profile if missing
                    if (!$user->doctor) {
                        Doctor::create([
                            'user_id' => $user->id,
                            'type' => 'Doctor',
                            'name' => $name,
                            'email' => $email,
                            'is_verified' => false,
                            'can_video_consult' => false,
                        ]);
                    }
                    // Update role to doctor
                    $user->role = 'doctor';
                    $user->save();
                } else {
                    // Ensure doctor profile exists for doctors
                    if (!$user->doctor) {
                        Doctor::create([
                            'user_id' => $user->id,
                            'type' => 'Doctor',
                            'name' => $name,
                            'email' => $email,
                            'is_verified' => false,
                            'can_video_consult' => false,
                        ]);
                    }
                }

                Auth::login($user);
                return redirect()->route('profile.index');
            }

            // Create new doctor user
            $newUser = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'doctor',
                'email_verified_at' => now(),
            ]);

            Doctor::create([
                'user_id' => $newUser->id,
                'type' => 'Doctor',
                'name' => $name,
                'email' => $email,
                'is_verified' => false,
                'can_video_consult' => false,
            ]);

            Auth::login($newUser);
            return redirect()->route('profile.index');
        } catch (\Exception $e) {
            return redirect()->route('p-signup')->with('error', 'Google authentication failed. Please try again.');
        }
    }
}
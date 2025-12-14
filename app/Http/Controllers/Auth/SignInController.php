<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\Doctor;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class SignInController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function showSignIn($recovery_type = null)
    {
        if ($recovery_type) {
            return view('auth.password-recovery', ['recovery_type' => $recovery_type]);
        }
        
        return view('auth.signin');
    }
    
    public function login(Request $request)
    {
        // Check if login is via email or phone
        if ($request->has('email')) {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                
                // Check if user is admin and redirect to admin dashboard
                if (Auth::user()->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                
                // Check if user is doctor or laboratory and redirect to profile page
                if (Auth::user()->isDoctor() || Auth::user()->isLaboratory()) {
                    return redirect()->route('profile.index');
                }
                
                return redirect()->intended('home');
            }
            
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->except('password'));
        } 
        else if ($request->has('phone_number') || ($request->has('login_type') && $request->input('login_type') === 'phone')) {
            $request->validate([
                'country_code' => 'required|string|max:5',
                'phone_number' => 'required|string|max:20',
                'password' => 'required',
            ]);
            
            $countryCode = $request->input('country_code');
            $phoneNumber = $request->input('phone_number');
            $fullPhoneNumber = $countryCode . $phoneNumber;
            
            // First check if there's a user with this phone number
            $user = User::where('phone_number', $phoneNumber)->first();
            
            // If not found in users table, check in profiles
            if (!$user) {
                $customer = Customer::where('phone_number', $phoneNumber)
                                  ->where('country_code', $countryCode)
                                  ->first();
                $doctor = Doctor::where('phone', $phoneNumber)
                               ->where('country_code', $countryCode)
                               ->first();
                
                if (!$customer && !$doctor) {
                    return back()->withErrors([
                        'phone_number' => 'No account found with this phone number.',
                    ])->withInput($request->except('password'));
                }
                
                $user = $customer ? $customer->user : ($doctor ? $doctor->user : null);
            }
            
            if ($user && Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();
                
                // Check if user is admin and redirect to admin dashboard
                if (Auth::user()->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                
                // Check if user is doctor or laboratory and redirect to profile page
                if (Auth::user()->isDoctor() || Auth::user()->isLaboratory()) {
                    return redirect()->route('profile.index');
                }
                
                return redirect()->intended('home');
            }
            
            return back()->withErrors([
                'phone_number' => 'The provided credentials do not match our records.',
            ])->withInput($request->except('password'));
        }
        
        return back()->withErrors([
            'error' => 'Invalid login attempt.',
        ]);
    }
    
    public function startPasswordRecovery(Request $request)
    {
        // Check if recovery is via email or phone
        if ($request->has('email')) {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);
            
            // Generate a random verification code (4 digits)
            $verificationCode = rand(1000, 9999);
            
            // Store recovery info in session
            Session::put('password_recovery', [
                'type' => 'email',
                'email' => $request->email,
                'verification_code' => $verificationCode,
            ]);
            
            // Send email OTP via SMTP
            Mail::raw('Your TravelCare verification code is: ' . $verificationCode, function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('TravelCare Password Recovery Code');
            });

            // Render OTP entry view without exposing the code
            return view('auth.recovery-otp', [
                'recovery_type' => 'email',
                'contact_info' => $request->email
            ]);
        } 
        else if ($request->has('phone_number')) {
            $request->validate([
                'country_code' => 'required|string|max:5',
                'phone_number' => 'required|string|max:20',
            ]);
            
            // Format the phone number with country code
            $fullPhoneNumber = $request->country_code . $request->phone_number;
            
            // Find user by phone number
            $customer = Customer::where('phone_number', $request->phone_number)
                                ->where('country_code', $request->country_code)
                                ->first();
            
            $doctor = Doctor::where('phone', $request->phone_number)
                           ->where('country_code', $request->country_code)
                           ->first();
            
            if (!$customer && !$doctor) {
                return back()->withErrors([
                    'phone_number' => 'No account found with this phone number.',
                ])->withInput();
            }
            
            // Generate a random verification code (4 digits)
            $verificationCode = rand(1000, 9999);
            
            // Store recovery info in session
            Session::put('password_recovery', [
                'type' => 'phone',
                'country_code' => $request->country_code,
                'phone_number' => $request->phone_number,
                'verification_code' => $verificationCode,
                'full_phone_number' => $fullPhoneNumber,
                'user_id' => $customer ? $customer->user_id : $doctor->user_id,
            ]);
            
            // Send OTP via Twilio
            $otpSent = $this->twilioService->sendOtp($fullPhoneNumber, $verificationCode);
            
            // Always render OTP entry view without exposing the code
            return view('auth.recovery-otp', [
                'recovery_type' => 'phone',
                'contact_info' => $fullPhoneNumber
            ]);
        }
        
        return back()->withErrors([
            'error' => 'Invalid recovery attempt.',
        ]);
    }
    
    public function verifyRecoveryOtp(Request $request)
    {
        // Combine the 4 digits into a single code
        $enteredCode = $request->code_1 . $request->code_2 . $request->code_3 . $request->code_4;
        
        // Get the stored verification code from session
        $storedCode = Session::get('password_recovery.verification_code');
        
        // Verify the entered code matches the stored code
        if ($this->twilioService->verifyOtp($enteredCode, $storedCode)) {
            // Mark the OTP as verified in the session
            Session::put('password_recovery.otp_verified', true);
            
            // Redirect to the password reset form
            return view('auth.new-password');
        }
        
        // If verification fails, redirect back with an error message
        return back()->withErrors([
            'error' => 'Invalid verification code. Please try again.',
        ]);
    }
    
    public function resetPassword(Request $request)
    {
        // Validate the passwords
        $request->validate([
            'new-password' => 'required|min:8',
            'confirm-password' => 'required|same:new-password',
        ]);
        
        // Check if OTP was verified
        if (!Session::has('password_recovery.otp_verified') || !Session::get('password_recovery.otp_verified')) {
            return redirect()->route('signin')->withErrors([
                'error' => 'Password reset session expired. Please try again.',
            ]);
        }
        
        // Get the recovery info from session
        $recoveryInfo = Session::get('password_recovery');
        
        // Find the user
        $user = null;
        
        if ($recoveryInfo['type'] === 'email') {
            $user = User::where('email', $recoveryInfo['email'])->first();
        } else {
            $user = User::find($recoveryInfo['user_id']);
        }
        
        if (!$user) {
            return redirect()->route('signin')->withErrors([
                'error' => 'User not found. Please try again.',
            ]);
        }
        
        // Update the password
        $user->password = Hash::make($request->input('new-password'));
        $user->save();
        
        // Clear the recovery session
        Session::forget('password_recovery');
        
        // Log the user in
        Auth::login($user);
        
        // Check if user is admin and redirect to admin dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Your password has been reset successfully!');
        }
        
        // Check if user is doctor or laboratory and redirect to profile page
        if ($user->isDoctor() || $user->isLaboratory()) {
            return redirect()->route('profile.index')->with('success', 'Your password has been reset successfully!');
        }
        
        // Redirect to the home page
        return redirect()->route('home')->with('success', 'Your password has been reset successfully!');
    }
    
    public function resendRecoveryOtp()
    {
        // Get the recovery info from session
        $recoveryInfo = Session::get('password_recovery');
        
        if (!$recoveryInfo) {
            return response()->json(['success' => false, 'message' => 'Recovery information not found']);
        }
        
        // Generate a new verification code (4 digits)
        $newVerificationCode = rand(1000, 9999);
        
        // Update the verification code in session
        $recoveryInfo['verification_code'] = $newVerificationCode;
        Session::put('password_recovery', $recoveryInfo);
        
        if ($recoveryInfo['type'] === 'email') {
            // Send email with new verification code via SMTP
            Mail::raw('Your new TravelCare verification code is: ' . $newVerificationCode, function ($message) use ($recoveryInfo) {
                $message->to($recoveryInfo['email'])
                        ->subject('TravelCare Password Recovery Code');
            });

            return response()->json([
                'success' => true,
                'message' => 'New verification code sent to your email'
            ]);
        } else {
            // Send the new OTP via Twilio
            $otpSent = $this->twilioService->sendOtp($recoveryInfo['full_phone_number'], $newVerificationCode);
            
            return response()->json(['success' => true, 'message' => 'New verification code sent to your phone']);
        }
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}

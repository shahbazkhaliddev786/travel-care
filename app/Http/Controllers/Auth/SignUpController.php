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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SignUpController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    /* Customer Signup Setup */
    public function customerSignup($p_name, Request $request)
    {
        if ($p_name == "general-info") {
            // Validate basic info form data
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:8',
            ]);
            
            // Store form data in session
            Session::put('basic_info', [
                'email' => $request->email,
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => $request->password,
            ]);
            
            return view('auth.customer-signup.general-info');
        }
        elseif ($p_name == "verify-number") {
            // Debug: Log the incoming request data
            \Log::info('Form submission data:', $request->all());
            
            // Validate general info form data
            $request->validate([
                'country' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'age' => 'required|integer',
                'weight' => 'required|numeric',
                'chronic_pathologies' => 'nullable|array',
                'chronic_pathologies.*' => 'nullable|string|max:255',
                'allergies' => 'nullable|array',
                'allergies.*' => 'nullable|string|max:255',
                'chronic_medications' => 'nullable|array',
                'chronic_medications.*' => 'nullable|string|max:255',
            ]);
            
            // Get gender value from the form
            $gender = $request->has('gender') ? $request->gender : 'Female';
            
            // Process array fields - filter out empty values and encode as JSON
            $chronicPathologies = $request->chronic_pathologies ? 
                json_encode(array_values(array_filter($request->chronic_pathologies, function($value) {
                    return !empty(trim($value));
                }))) : null;
                
            $allergies = $request->allergies ? 
                json_encode(array_values(array_filter($request->allergies, function($value) {
                    return !empty(trim($value));
                }))) : null;
                
            $chronicMedications = $request->chronic_medications ? 
                json_encode(array_values(array_filter($request->chronic_medications, function($value) {
                    return !empty(trim($value));
                }))) : null;
            
            // Debug: Log processed data
            \Log::info('Processed array data:', [
                'chronic_pathologies' => $chronicPathologies,
                'allergies' => $allergies,
                'chronic_medications' => $chronicMedications
            ]);
            
            // Store form data in session
            Session::put('general_info', [
                'country' => $request->country,
                'city' => $request->city,
                'gender' => $gender,
                'age' => $request->age,
                'weight' => $request->weight,
                'chronic_pathologies' => $chronicPathologies,
                'allergies' => $allergies,
                'chronic_medications' => $chronicMedications,
            ]);
            
            return view('auth.customer-signup.verify-number');
        }
        elseif ($p_name == "otp") {
            // Validate phone verification form data
            $request->validate([
                'country_code' => 'required|string|max:5',
                'phone_number' => 'required|string|max:20',
            ]);
            
            // Generate a random verification code (4 digits) as string
            $verificationCode = (string) rand(1000, 9999);
            
            // Format the phone number with country code
            $fullPhoneNumber = $request->country_code . $request->phone_number;
            
            // Store form data in session
            Session::put('verification_info', [
                'country_code' => $request->country_code,
                'phone_number' => $request->phone_number,
                'verification_code' => $verificationCode,
                'full_phone_number' => $fullPhoneNumber,
            ]);
            
            // Get all the stored form data from session
            $basicInfo = Session::get('basic_info');
            $generalInfo = Session::get('general_info');
            $verificationInfo = Session::get('verification_info');
            
            // Create a new user
            $user = User::create([
                'name' => $basicInfo['name'],
                'email' => $basicInfo['email'],
                'country_code' => $verificationInfo['country_code'],
                'phone_number' => $verificationInfo['phone_number'],
                'password' => Hash::make($basicInfo['password']),
                'is_active' => false, // Inactive until verified by admin
            ]);
            
            // Create a new customer profile
            $customerData = [
                'user_id' => $user->id,
                'name' => $basicInfo['name'],
                'email' => $basicInfo['email'],
                'phone' => $basicInfo['phone'],
                'country' => $generalInfo['country'],
                'city' => $generalInfo['city'],
                'gender' => $generalInfo['gender'],
                'age' => $generalInfo['age'],
                'weight' => $generalInfo['weight'],
                'chronic_pathologies' => $generalInfo['chronic_pathologies'],
                'allergies' => $generalInfo['allergies'],
                'chronic_medications' => $generalInfo['chronic_medications'],
                'country_code' => $verificationInfo['country_code'],
                'phone_number' => $verificationInfo['phone_number'],
                'verification_code' => $verificationInfo['verification_code'],
                'is_verified' => false,
            ];
            
            // Debug: Log customer data before creation
            \Log::info('Creating customer with data:', $customerData);
            
            $customer = Customer::create($customerData);
            
            // Debug: Log created customer
            \Log::info('Customer created successfully:', ['id' => $customer->id, 'chronic_pathologies' => $customer->chronic_pathologies]);
            
            // Send OTP via Twilio
            $otpSent = $this->twilioService->sendOtp($fullPhoneNumber, $verificationCode);
            
            // If in development environment or if OTP sending fails, show code for reference but don't auto-fill
            if (config('app.env') === 'local' || !$otpSent) {
                return view('auth.customer-signup.otp', [
                    'verification_code' => $verificationCode,
                    'show_code_only' => true
                ]);
            }
            
            return view('auth.customer-signup.otp');
        }
        else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    /**
     * Show the customer OTP verification page (GET request)
     */
    public function showCustomerOtp()
    {
        // Check if there's verification info in session
        $verificationInfo = Session::get('verification_info');
        
        if (!$verificationInfo) {
            // If no verification info exists, redirect to signup
            return redirect('/customer-signup/basic-info')->with('error', 'Please complete the signup process first.');
        }
        
        // Only show verification code for reference in development environment, don't auto-fill
        if (config('app.env') === 'local') {
            return view('auth.customer-signup.otp', [
                'verification_code' => $verificationInfo['verification_code'],
                'show_code_only' => true
            ]);
        }
        
        return view('auth.customer-signup.otp');
    }
    
    /**
     * Verify the OTP code entered by the user.
     */
    public function verifyOtp(Request $request)
    {
        // Validate that all 4 code fields are provided
        $request->validate([
            'code_1' => 'required|numeric|digits:1',
            'code_2' => 'required|numeric|digits:1',
            'code_3' => 'required|numeric|digits:1',
            'code_4' => 'required|numeric|digits:1',
        ]);

        // Combine the 4 digits into a single code
        $enteredCode = $request->code_1 . $request->code_2 . $request->code_3 . $request->code_4;
        
        // Get the stored verification code from session
        $storedCode = Session::get('verification_info.verification_code');
        $email = Session::get('basic_info.email');
        
        // Check if required session data exists
        if (!$storedCode || !$email) {
            return redirect('/customer-signup/basic-info')->with('error', 'Session expired. Please start the signup process again.');
        }
        
        // Ensure both codes are strings for proper comparison
        $enteredCode = (string) $enteredCode;
        $storedCode = (string) $storedCode;
        
        // Add comprehensive debug logging for development
        if (config('app.env') === 'local') {
            \Log::info('OTP Verification Debug', [
                'entered_code' => $enteredCode,
                'entered_code_type' => gettype($enteredCode),
                'entered_code_length' => strlen($enteredCode),
                'stored_code' => $storedCode,
                'stored_code_type' => gettype($storedCode),
                'stored_code_length' => strlen($storedCode),
                'email' => $email,
                'codes_match_strict' => $enteredCode === $storedCode,
                'codes_match_loose' => $enteredCode == $storedCode,
                'session_verification_info' => Session::get('verification_info'),
                'request_data' => [
                    'code_1' => $request->code_1,
                    'code_2' => $request->code_2,
                    'code_3' => $request->code_3,
                    'code_4' => $request->code_4,
                ]
            ]);
        }
        
        // Direct comparison as fallback (since TwilioService just does string comparison anyway)
        $codesMatch = ($enteredCode === $storedCode);
        
        // Verify the entered code matches the stored code
        if ($codesMatch || $this->twilioService->verifyOtp($enteredCode, $storedCode)) {
            // Find the user
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return redirect('/customer-signup/basic-info')->with('error', 'User not found. Please start the signup process again.');
            }
            
            // Update the customer profile to mark as verified
            $customer = Customer::where('user_id', $user->id)->first();
            if (!$customer) {
                return redirect('/customer-signup/basic-info')->with('error', 'Customer profile not found. Please start the signup process again.');
            }
            
            $customer->is_verified = true;
            $customer->save();
            
            // Log the user in
            Auth::login($user);
            
            // Clear the session data
            Session::forget(['basic_info', 'general_info', 'verification_info']);
            
            // Check if user is doctor or laboratory and redirect to profile page
            if ($user->role === 'doctor' || $user->role === 'laboratory') {
                return redirect()->route('profile.index')->with('success', 'Your account has been verified successfully!');
            }
            
            // Redirect to the home page
            return redirect()->route('home')->with('success', 'Account verified successfully!');
        }
        
        // If verification fails, redirect back with an error message
        return redirect()->back()->with('error', 'Invalid verification code. Please try again.');
    }

    /**
     * Resend OTP to the user's phone number
     */
    public function resendOtp()
    {
        // Get the verification info from session
        $verificationInfo = Session::get('verification_info');
        
        if (!$verificationInfo) {
            return response()->json(['success' => false, 'message' => 'Verification information not found']);
        }
        
        // Generate a new verification code (4 digits) as string
        $newVerificationCode = (string) rand(1000, 9999);
        
        // Update the verification code in session
        $verificationInfo['verification_code'] = $newVerificationCode;
        Session::put('verification_info', $verificationInfo);
        
        // Update the verification code in the database
        $email = Session::get('basic_info.email');
        $user = User::where('email', $email)->first();
        
        if ($user) {
            $customer = Customer::where('user_id', $user->id)->first();
            if ($customer) {
                $customer->verification_code = $newVerificationCode;
                $customer->save();
            }
        }
        
        // Send the new OTP via Twilio
        $otpSent = $this->twilioService->sendOtp($verificationInfo['full_phone_number'], $newVerificationCode);
        
        if (config('app.env') === 'local' || !$otpSent) {
            return response()->json([
                'success' => true, 
                'message' => 'New verification code sent', 
                'code' => $newVerificationCode
            ]);
        }
        
        return response()->json(['success' => true, 'message' => 'New verification code sent']);
    }


    /* Professional Sign Up Setup */
    /**
     * Handle professional basic info form submission
     */
    public function professionalBasicInfo(Request $request)
    {
        // Validate the basic info
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'country_code' => 'required|string|max:5',
            'phone_number' => 'required|string|max:20',
            'password' => 'required|string|min:8',
        ]);
        
        // Store the basic info in session
        Session::put('professional_basic_info', [
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'country_code' => $request->country_code,
            'password' => $request->password,
        ]);
        
        // Redirect to general info page
        return redirect()->route('p-general-info');
    }

    public function professionalSignup(Request $request)
    {
        // Get the basic info from the session
        $basicInfo = Session::get('professional_basic_info');
        
        // If basic info is not in the session, redirect to basic info page
        if (!$basicInfo) {
            return redirect()->route('p-signup')->with('error', 'Please complete the basic information first.');
        }

        
        // If we reached here, it means we're processing the general info form
        // Validate the general info form
        $request->validate([
            'acc-type' => 'required|in:Doctor,Laboratory',
            'city' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'p-id' => 'required|string|max:50', // Professional Mexican ID
            'license' => 'required|string', // Mexican Voting License Scan
            'address' => 'required|string',
        ]);
        
        // Create a new user with role based on account type
        $role = $request->input('acc-type') === 'Doctor' ? 'doctor' : 'laboratory';
        
        $user = User::create([
            'name' => $request->name,
            'email' => $basicInfo['email'],
            'country_code' => $basicInfo['country_code'],
            'phone_number' => $basicInfo['phone_number'],
            'password' => Hash::make($basicInfo['password']),
            'role' => $role,
            'is_active' => false, // Inactive until verified by admin
        ]);
        
        // Create a professional profile
        Doctor::create([
            'user_id' => $user->id,
            'type' => $request->input('acc-type'),
            'name' => $request->name,
            'email' => $basicInfo['email'],
            'phone' => $basicInfo['phone_number'],
            'country_code' => $basicInfo['country_code'],
            'city' => $request->city,
            'professional_id' => $request->input('p-id'),
            'license_scan' => $request->license,
            'address' => $request->address,
            'is_verified' => false, // Doctors need to be verified by admin
            'can_video_consult' => false, // Disabled until verified and explicitly enabled
        ]);
        
        // Log the user in
        Auth::login($user);
        
        // Clear the session data
        Session::forget('professional_basic_info');
        
        // Redirect to the profile page for doctors and laboratories
        return redirect()->route('profile.index')->with('success', 'Your professional account has been created successfully!');
    }

    public function createAccount() {
        return view('home');
    }
}

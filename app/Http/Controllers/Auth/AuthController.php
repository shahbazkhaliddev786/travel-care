<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function showGetStarted()
    {
        return view('auth.getstart');
    }
    
    public function showCustomerSignup()
    {
        return view('auth.customer-signup.basic-info');
    }

    public function showProfessionalSignup()
    {
        return view('auth.professional-signup.basic-info');
    }

    public function showProfessionalGeneralInfo()
    {
        // Check if basic info exists in session
        $basicInfo = session('professional_basic_info');
        
        if (!$basicInfo) {
            return redirect()->route('p-signup')->with('error', 'Please complete the basic information first.');
        }
        
        return view('auth.professional-signup.general-info');
    }

    public function showSignIn() {
        return view('auth.signin');
    }
}

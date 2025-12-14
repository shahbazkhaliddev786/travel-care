@extends('layouts.getstartedlayout')

@section('title', 'TravelCare - Get Started')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <!-- Get Started Content -->
            <div class="getstarted-content">
                <img src="{{ asset('assets/logo-with-txt.svg') }}" alt="TravelCare Logo" class="main-logo">
                <h1 class="title">Create An Account To<br>Get Started!</h1>
                <button onclick="window.location.href='{{ route('p-signup') }}'" id="professionalBtn" class="btn btn-primary">Create A Professional Account</button>
                <button onclick="window.location.href='{{ route('c-signup') }}'" id="customerBtn" class="btn btn-primary">Create Free Customer Account</button>
                <div class="login-link">
                    <a href="{{ route('signin') }}" id="showLogin">Already have an account?</a>
                </div>
            </div>
        </div>
    </div>
@endsection
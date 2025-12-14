@extends('layouts.getstartedlayout')

@section('title', 'Password Recovery')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <a href="{{ route('signin') }}" id="prevButton"><img src="/assets/icons/arrow-left.svg" alt="return" class="arr-back"></a>
            
            
            <!-- Customer Sign Up Content 3rd Box-->
            <div class="signin-content" id="box3">
                <h2 class="title secondary">Password Recovery</h2>

                @if($recovery_type == 'email')
                    <p>Enter your registered e-mail address. <br> You will be sent an email to reset your password.</p>
                @else
                    <p>Enter your registered phone number. <br> You will be sent an SMS with a verification code.</p>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Account Activation Form -->
                <form class="signin-form" action="{{ route('start-recovery') }}" method="POST">
                    @csrf
                    <input type="hidden" name="recovery_type" value="{{ $recovery_type }}">

                    @if($recovery_type == 'email')
                        
                        <x-input-field name="email" type="email" icon="/assets/icons/mail.svg" placeholder="Email" />

                        <button class="btn btn-primary">Send Verification Code</button>
                    @else
                        <div class="input-group">
                            <select name="country_code" class="country-code">
                                @foreach(config('countries.country_codes') as $code => $country)
                                    <option value="{{ $code }}">{{ $code }}</option>
                                @endforeach
                            </select>
                            <span>|</span>
                            <input type="tel" name="phone_number" placeholder="00 000 00 00" pattern="[0-9\s]{10,}" required>
                        </div>
                        
                        <button class="btn btn-primary">Send Verification Code</button>
                    @endif

                    <div class="signup-link">
                        <p>Don't have an account?</p>
                        <a href="{{ route('get-started') }}">Sign Up</a>
                    </div>
                </form>
            </div>



        </div>
    </div>
@endsection
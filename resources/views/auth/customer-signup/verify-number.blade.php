@extends('layouts.getstartedlayout')

@section('title', 'TravelCare - Sign Up')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <a href="{{ route('c-signup') }}" id="prevButton"><img src="/assets/icons/arrow-left.svg" alt="return" class="arr-back"></a>
            
            
            <!-- Customer Sign Up Content 3rd Box-->
            <div class="signin-content" id="box3">
                <h2 class="title secondary">Account Activation</h2>
                <p>Enter your phone number, a verification code will be sent to you</p>

                <!-- Account Activation Form -->
                <form class="signin-form" action="{{ route('c-info', ['p_name' => 'otp']) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <select name="country_code" class="country-code">
                            @foreach(config('countries.country_codes') as $code => $country)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                        <span>|</span>
                        <input type="tel" name="phone_number" placeholder="00 000 00 00" pattern="[0-9\s]{10,}" required>
                    </div>
                    
                    <button class="btn btn-primary">Send A Verification Code</button>
                </form>
            </div>



        </div>
    </div>
@endsection
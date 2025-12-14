@extends('layouts.getstartedlayout')

@section('title', 'TravelCare - Sign Up')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <a href="{{ route('get-started') }}" id="prevButton"><img src="/assets/icons/arrow-left.svg" alt="return" class="arr-back"></a>
            
            <!-- Sign In Content -->
            <div class="signin-content animated-container">
                <img src="/assets/logo.svg" alt="TravelCare Logo" class="signin-logo">
                <h1 class="title"> <span>Welcome to</span><br>TravelCare</h1>

                <!-- Sign In Form -->
                <form class="signin-form" action="{{ route('p-basic-info') }}" method="POST">
                    @csrf
                    
                    <x-input-field name="email" type="email" icon="/assets/icons/mail.svg" placeholder="Email" />
                    
                    <div class="input-group">
                        <select name="country_code" class="country-code">
                            @foreach(config('countries.country_codes') as $code => $country)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                        <span>|</span>
                        <input type="tel" name="phone_number" placeholder="00 000 00 00" pattern="[0-9\s]{10,}" required>
                    </div>

                
                    <div class="field-box">
                        <div class="input-group @error('password') error-border @enderror">
                            <img src="/assets/icons/lock.svg" alt="Password" class="input-icon">
                            <input type="password" name="password" placeholder="Password" required>
                            <button type="button" class="toggle-password">
                                <img src="/assets/icons/hide.svg" alt="Show Password">
                            </button>
                        </div>

                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <button class="btn btn-primary">Sign Up</button>
                </form>

                <div class="divider">
                    <span>or sign up with</span>
                </div>

                <div class="social-signin">
                    <button class="btn btn-social google" type="button" onclick="window.location.href='{{ route('auth.google.redirect') }}'">
                        <img src="/assets/icons/google.svg" alt="Google">
                    </button>
                    <button class="btn btn-social facebook" type="button" onclick="window.location.href='{{ route('auth.facebook.redirect') }}'">
                        <img src="/assets/icons/fb.svg" alt="Facebook">
                    </button>
                </div>
            </div>
            

        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle visibility
        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                // Prevent event bubbling to avoid conflicts
                e.preventDefault();
                e.stopPropagation();
                
                // Find the password field within the same input-group
                const passwordField = this.closest('.input-group').querySelector('input[type="password"], input[type="text"]');
                const icon = this.querySelector('img');
                
                if (passwordField && icon) {
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        icon.src = '/assets/icons/un-hide.svg';
                        icon.alt = 'Hide Password';
                    } else {
                        passwordField.type = 'password';
                        icon.src = '/assets/icons/hide.svg';
                        icon.alt = 'Show Password';
                    }
                }
            });
        });
    });
</script>
@endsection
@extends('layouts.getstartedlayout')

@section('title', 'TravelCare - Sign In')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <a href="{{ route('get-started') }}" id="prevButton"><img src="/assets/icons/arrow-left.svg" alt="return" class="arr-back"></a>
            
            <!-- Sign In Content -->
            <div class="signin-content">
                <img src="assets/logo.svg" alt="TravelCare Logo" class="signin-logo">
                <h1 class="title"> <span>Welcome back to</span><br>TravelCare</h1>
                
                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
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
                
                <div class="signin-tabs">
                    <button class="tab-btn active tab1" data-tab="email">Sign In With Email</button>
                    <button class="tab-btn tab2" data-tab="phone">Sign In With Phone Number</button>
                </div>

                <!-- Email Sign In Form -->
                <form class="signin-form email-form" action="{{ route('login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="login_type" value="email">
                    
                    <x-input-field name="email" type="email" icon="/assets/icons/mail.svg" placeholder="Email" />
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
                    <a href="{{ route('recover', ['recovery_type' => 'email']) }}" class="forgot-password">Forgot Password?</a>
                    <button type="submit" class="btn btn-primary">Sign In</button>
                </form>

                <!-- Phone Sign In Form -->
                <form class="signin-form phone-form hidden" action="{{ route('login') }}" method="POST">
                    @csrf
                    <input type="hidden" name="login_type" value="phone">
                    
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
                    <a href="{{ route('recover', ['recovery_type' => 'phone']) }}" class="forgot-password">Forgot Password?</a>
                    <button type="submit" class="btn btn-primary">Sign In</button>
                </form>

                <div class="divider">
                    <span>or sign in with</span>
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
        const tabBtns = document.querySelectorAll('.tab-btn');
        const emailForm = document.querySelector('.email-form');
        const phoneForm = document.querySelector('.phone-form');
        
        // Tab Switching
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all tabs
                tabBtns.forEach(b => b.classList.remove('active'));
                // Add active class to clicked tab
                btn.classList.add('active');

                // Show/hide appropriate form
                const isPhone = btn.getAttribute('data-tab') === 'phone';
                emailForm.classList.toggle('hidden', isPhone);
                phoneForm.classList.toggle('hidden', !isPhone);
            });
        });

        // Password toggle visibility - fixed implementation
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
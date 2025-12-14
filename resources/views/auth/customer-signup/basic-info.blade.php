@extends('layouts.getstartedlayout')

@section('title', 'TravelCare - Sign Up')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <a href="#" id="prevButton"><img src="/assets/icons/arrow-left.svg" alt="return" class="arr-back"></a>
            
            
            <!-- Customer Sign Up Content 1st Box-->
            <div class="signin-content" id="box1">
                <img src="/assets/logo.svg" alt="TravelCare Logo" class="signin-logo">
                <h1 class="title"> <span>Welcome to</span><br>TravelCare</h1>

                <!-- Email Sign Up Form -->
                <form class="signin-form animated-container" action="{{ route('c-info', ['p_name' => 'general-info']) }}" method="POST">
                    @csrf
                    
                    <x-input-field name="email" type="email" icon="/assets/icons/mail.svg" placeholder="Email" />
                    
                    <x-input-field name="name" type="text" icon="/assets/icons/user.svg" placeholder="Full Name" />
                    
                    <x-input-field name="phone" type="tel" icon="/assets/icons/phone.svg" placeholder="Phone Number" />
                    
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
                    <button class="btn btn-primary">Next</button>
                </form>
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
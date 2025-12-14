@extends('layouts.getstartedlayout')

@section('title', 'TravelCare - Sign Up')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('c-signup') }}" id="prevButton"><img src="/assets/icons/arrow-left.svg" alt="return" class="arr-back"></a>
            

            
            
            <!-- Customer Sign Up Content 4th Box-->
            <div class="signin-content" id="box4">
                <h2 class="title secondary">Enter A Code</h2>
                <p>We've sent a verification code to your number <br> 
                    <span class="phone-number">{{ session('verification_info.country_code') }} {{ session('verification_info.phone_number') }}</span>
                </p>

                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('verify-otp') }}" method="POST" id="otpForm">
                    @csrf
                    <div class="code-inputs">
                        <input type="text" name="code_1" maxlength="1" class="otp-input" autofocus>
                        <input type="text" name="code_2" maxlength="1" class="otp-input">
                        <input type="text" name="code_3" maxlength="1" class="otp-input">
                        <input type="text" name="code_4" maxlength="1" class="otp-input">
                    </div>

                    <div id="verification-status"></div>

                    <div class="timer">
                        <p>You will be able to request a new code in <span id="countdown">1:15</span></p>
                        <button type="button" class="resend-btn" id="resendBtn" disabled>Resend Code</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.otp-input');
        const form = document.getElementById('otpForm');
        const statusDiv = document.getElementById('verification-status');
        const resendBtn = document.getElementById('resendBtn');
        const countdownEl = document.getElementById('countdown');
        
        let countdown = 75; // 1:15 in seconds
        let countdownTimer;
        
        // Start the countdown
        startCountdown();
        
        // Function to start the countdown timer
        function startCountdown() {
            updateCountdownDisplay();
            
            countdownTimer = setInterval(function() {
                countdown--;
                updateCountdownDisplay();
                
                if (countdown <= 0) {
                    clearInterval(countdownTimer);
                    resendBtn.disabled = false;
                }
            }, 1000);
        }
        
        // Update the countdown display
        function updateCountdownDisplay() {
            const minutes = Math.floor(countdown / 60);
            const seconds = countdown % 60;
            countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        
        // Resend OTP
        resendBtn.addEventListener('click', function() {
            if (!this.disabled) {
                statusDiv.innerHTML = '<div class="alert alert-info">Sending new code...</div>';
                this.disabled = true;
                
                // Reset countdown
                countdown = 75;
                startCountdown();
                
                // Create a form data object for the CSRF token
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                
                // Send AJAX request to resend OTP
                fetch('{{ route("resend-otp") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusDiv.innerHTML = '<div class="alert alert-success">New code sent successfully!</div>';
                        
                        // Clear the input fields for new code
                        inputs.forEach(input => {
                            input.value = '';
                        });
                        
                        // Focus on the first input
                        inputs[0].focus();
                    } else {
                        statusDiv.innerHTML = `<div class="alert alert-error">${data.message}</div>`;
                        resendBtn.disabled = false;
                    }
                })
                .catch(error => {
                    statusDiv.innerHTML = '<div class="alert alert-error">Failed to resend code. Please try again.</div>';
                    resendBtn.disabled = false;
                    console.error('Error:', error);
                });
            }
        });
        
        // Auto-focus next input after entering a digit
        inputs.forEach((input, index) => {
            // Handle input events
            input.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Move to next input if a digit is entered
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                // Check if all inputs are filled
                let allFilled = true;
                let code = '';
                
                inputs.forEach(input => {
                    if (input.value.length === 0) {
                        allFilled = false;
                    }
                    code += input.value;
                });
                
                // Submit form if all inputs are filled (automatically)
                if (allFilled) {
                    // Show verification state immediately
                    statusDiv.innerHTML = '<div class="alert alert-info">Verifying code...</div>';
                    
                    // Submit the form immediately for automatic verification
                    setTimeout(() => {
                        form.submit();
                    }, 300); // Small delay for visual feedback
                }
            });
            
            // Handle paste event for the entire code
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                
                // Get pasted data
                const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                
                // Check if pasted data is a 4-digit number
                if (/^\d{4}$/.test(pastedData)) {
                    // Fill all inputs with the respective digits
                    for (let i = 0; i < inputs.length; i++) {
                        inputs[i].value = pastedData.charAt(i);
                    }
                    
                    // Show verification state immediately
                    statusDiv.innerHTML = '<div class="alert alert-info">Verifying code...</div>';
                    
                    // Submit the form immediately for automatic verification
                    setTimeout(() => {
                        form.submit();
                    }, 300); // Small delay for visual feedback
                }
            });
            
            // Handle backspace to go to previous input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
            
            // Handle focus to select text
            input.addEventListener('focus', function() {
                this.select();
            });
        });
        
        // Auto-fill functionality removed to ensure manual entry
        // Users must manually enter the OTP code
    });
</script>
@endsection
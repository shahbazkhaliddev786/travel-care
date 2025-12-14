@extends('layouts.getstartedlayout')

@section('title', 'Password Recovery')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <a href="#" id="prevButton"><img src="/assets/icons/arrow-left.svg" alt="return" class="arr-back"></a>
            
            
            <!-- Customer Sign Up Content 3rd Box-->
            <div class="signin-content"">
                <h2 class="title secondary">New password</h2>
                <p>Enter a new password</p>

                @if($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- New Password Form -->
                <form class="signin-form" action="{{ route('reset-password') }}" method="POST">
                    @csrf

                    <div class="label-input">
                        <label for="">Enter a new password</label>
                        <div class="field-box">
                            <div class="input-group @error('new-password') error-border @enderror">
                                <input type="password" name="new-password" placeholder="New Password" required>
                                <button type="button" class="toggle-password">
                                    <img src="/assets/icons/hide.svg" alt="Show Password">
                                </button>
                            </div>
    
                            @error('new-password')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="label-input">
                        <label for="">Confirm password</label>
                        <div class="field-box">
                            <div class="input-group @error('confirm-password') error-border @enderror">
                                <input type="password" name="confirm-password" placeholder="Confirm Password" required>
                                <button type="button" class="toggle-password">
                                    <img src="/assets/icons/hide.svg" alt="Show Password">
                                </button>
                            </div>
    
                            @error('confirm-password')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <button class="btn btn-primary">Confirm</button>
                </form>
            </div>



        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Prevent event bubbling to avoid conflicts
                e.preventDefault();
                e.stopPropagation();
                
                // Find the password field within the same input-group
                const input = this.closest('.input-group').querySelector('input[type="password"], input[type="text"]');
                const img = this.querySelector('img');
                
                if (input && img) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        img.src = '/assets/icons/un-hide.svg';
                        img.alt = 'Hide Password';
                    } else {
                        input.type = 'password';
                        img.src = '/assets/icons/hide.svg';
                        img.alt = 'Show Password';
                    }
                }
            });
        });
    });
</script>
@endsection
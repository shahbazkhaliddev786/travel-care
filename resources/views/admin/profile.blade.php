@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('content')
<div class="row">
    <div class="col-md-6">
        <!-- Profile Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Profile Information</h6>
            </div>
            <div class="card-body">

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    <!-- Debug info -->
                    <input type="hidden" name="debug" value="profile_form_submitted">
                    <div class="mb-3 text-center">
                        <div class="profile-image-container mb-3">
                            <div class="profile-image-wrapper" id="profileImageWrapper">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile Photo" class="profile-image" id="profilePreview">
                                @else
                                    <img src="{{ asset('assets/icons/default-avatar.svg') }}" alt="Profile Photo" class="profile-image" id="profilePreview">
                                @endif
                                <div class="profile-image-overlay">
                                    <i class="bi bi-camera"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Profile Photo</label>
                            <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*" onchange="previewProfileImage(this)">
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Upload a new profile photo (JPG, PNG, GIF - Max 2MB)</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="updateProfileBtn">
                        <i class="bi bi-check-circle me-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Change Password -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Change Password</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update-password') }}" method="POST" id="passwordForm">
                    @csrf
                    <!-- Debug info -->
                    <input type="hidden" name="debug" value="password_form_submitted">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary toggle-btn" type="button" onclick="togglePassword('current_password')">
                                <i class="bi bi-eye" id="current_password_icon"></i>
                            </button>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-btn" type="button" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="password_icon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Password must be at least 8 characters long</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                            <button class="btn btn-outline-secondary toggle-btn" type="button" onclick="togglePassword('password_confirmation')">
                                <i class="bi bi-eye" id="password_confirmation_icon"></i>
                            </button>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Account Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Account Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <p><span class="badge bg-primary">Admin</span></p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Account Created</label>
                    <p>{{ Auth::user()->created_at->format('F d, Y') }}</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Last Updated</label>
                    <p>{{ Auth::user()->updated_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-image-container {
    position: relative;
    display: inline-block;
}

.profile-image-wrapper {
    position: relative;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
}

.profile-image-wrapper:hover {
    transform: scale(1.05);
}

.profile-image {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    border: 3px solid #dee2e6;
}

.profile-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 50%;
}

.profile-image-wrapper:hover .profile-image-overlay {
    opacity: 1;
}

.profile-image-overlay i {
    color: white;
    font-size: 2rem;
}

.input-group .btn {
    border-left: 0;
}

.input-group .form-control {
    border-right: 0;
}

.input-group .form-control:focus {
    box-shadow: none;
    border-color: #ced4da;
}

.input-group .form-control:focus + .btn {
    border-color: #86b7fe;
}

.form-control.is-valid,
.form-control.is-invalid {
    margin-right: calc(1.5rem + 0.75rem);
}

.alert {
    margin-bottom: 1rem;
}

.btn i {
    margin-right: 0.25rem;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
}

.btn-primary-submitting {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}
</style>

<script>
// Preview selected profile image
function previewProfileImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Form submission handling
document.addEventListener('DOMContentLoaded', function() {
    // Add form submission logging
    const profileForm = document.getElementById('profileForm');
    const passwordForm = document.getElementById('passwordForm');
    
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            console.log('Profile form submitting...');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            console.log('CSRF token:', document.querySelector('input[name="_token"]').value);
            
            // Show visual feedback
            const btn = document.getElementById('updateProfileBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            btn.disabled = true;
            
            // Let the form submit naturally
        });
    }
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            console.log('Password form submitting...');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            
            // Show visual feedback
            const btn = document.getElementById('changePasswordBtn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Changing...';
            btn.disabled = true;
            
            // Let the form submit naturally
        });
    }

    // Real-time password confirmation validation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (password && confirmPassword && password !== confirmPassword) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (password && confirmPassword && password === confirmPassword) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-invalid', 'is-valid');
        }
    });

    // Profile image wrapper click handler
    document.getElementById('profileImageWrapper').addEventListener('click', function() {
        document.getElementById('profile_photo').click();
    });
});
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
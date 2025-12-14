@extends('layouts.admin')

@section('title', 'Add New Laboratory')
@section('subtitle', 'Create a new laboratory profile with complete information')

@section('actions')
<a href="{{ route('admin.laboratories.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-2"></i>
    Back to Laboratories
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-flask me-2"></i>
                    Add New Laboratory
                </h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="laboratoryForm" action="{{ route('admin.laboratories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-building me-2"></i>
                                Basic Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Laboratory Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="country_code" class="form-label">Country Code <span class="text-danger">*</span></label>
                                <select class="form-select @error('country_code') is-invalid @enderror" id="country_code" name="country_code" required>
                                    <option value="">Select Code</option>
                                    @foreach(config('countries.country_codes') as $code => $country)
                                        <option value="{{ $code }}" {{ old('country_code') == $code ? 'selected' : '' }}>{{ $code }} ({{ $country }})</option>
                                    @endforeach
                                </select>
                                @error('country_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-9">
                            <div class="form-group mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control pswd-input @error('password') is-invalid @enderror" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="passwordToggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control pswd-input @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye" id="password_confirmationToggleIcon"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Laboratory Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-clipboard-data me-2"></i>
                                Laboratory Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="professional_mexican_id" class="form-label">Professional Mexican ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('professional_mexican_id') is-invalid @enderror" id="professional_mexican_id" name="professional_mexican_id" value="{{ old('professional_mexican_id') }}" required placeholder="Enter Professional Mexican ID">
                                @error('professional_mexican_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="mexican_voting_license_scan" class="form-label">Mexican Voting License Scan</label>
                                <input type="text" class="form-control @error('mexican_voting_license_scan') is-invalid @enderror" id="mexican_voting_license_scan" name="mexican_voting_license_scan" value="{{ old('mexican_voting_license_scan') }}" placeholder="Enter Mexican voting license scan details">
                                @error('mexican_voting_license_scan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small>Enter the Mexican voting license scan information</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Laboratory Image Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-image me-2"></i>
                                Laboratory Image
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="profile_image" class="form-label">Profile Image</label>
                                <input type="file" 
                                       class="form-control @error('profile_image') is-invalid @enderror" 
                                       id="profile_image" 
                                       name="profile_image" 
                                       accept="image/*"
                                       onchange="previewCreateProfileImage(this)">
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small>Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="text-center">
                                <label class="form-label">Preview</label>
                                <div class="profile-image-preview-container">
                                    <img id="profile-image-create-preview" 
                                         src="{{ asset('assets/icons/default-avatar.svg') }}" 
                                         alt="Profile Preview" 
                                         class="img-fluid rounded-circle border" 
                                         style="max-width: 120px; height: 120px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-geo-alt me-2"></i>
                                Location Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="state" class="form-label">State/Province</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" required placeholder="Complete address...">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Operating Hours -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-clock me-2"></i>
                                Operating Hours
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="operating_hours_from" class="form-label">Operating Hours From</label>
                                <input type="time" class="form-control @error('operating_hours_from') is-invalid @enderror" id="operating_hours_from" name="operating_hours_from" value="{{ old('operating_hours_from', '08:00') }}">
                                @error('operating_hours_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="operating_hours_to" class="form-label">Operating Hours To</label>
                                <input type="time" class="form-control @error('operating_hours_to') is-invalid @enderror" id="operating_hours_to" name="operating_hours_to" value="{{ old('operating_hours_to', '17:00') }}">
                                @error('operating_hours_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Operating Days</label>
                                <div class="row">
                                    @php
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        $oldDays = old('operating_days', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
                                    @endphp
                                    @foreach($days as $day)
                                        <div class="col-md-3 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="operating_days[]" value="{{ $day }}" id="day_{{ $day }}" {{ in_array($day, $oldDays) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="day_{{ $day }}">
                                                    {{ $day }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                <div>
                                    <p class="mb-0 text-muted">
                                        <i class="bi bi-info-circle me-2"></i>
                                        All required fields must be filled before submission
                                    </p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.laboratories.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitButton">
                                        <i class="bi bi-flask me-2"></i>
                                        Create Laboratory
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Prevent the admin layout's button loading interference
    document.addEventListener('DOMContentLoaded', function() {
        const submitButton = document.getElementById('submitButton');
        if (submitButton) {
            submitButton.addEventListener('click', function(e) {
                // Stop the event from bubbling up to the admin layout's handler
                e.stopPropagation();
            });
        }
    });

    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + 'ToggleIcon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }

    // Form validation
    document.getElementById('laboratoryForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const submitButton = document.getElementById('submitButton');
        
        // Check password match
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            
            // Add error styling
            document.getElementById('password_confirmation').classList.add('is-invalid');
            return false;
        }

        // Show loading state
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating...';
        submitButton.disabled = true;
        
        return true;
    });

    // Real-time password confirmation validation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });

    // Profile image preview function for create form
    function previewCreateProfileImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('profile-image-create-preview').src = e.target.result;
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
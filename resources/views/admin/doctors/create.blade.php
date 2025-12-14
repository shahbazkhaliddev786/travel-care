@extends('layouts.admin')

@section('title', 'Add New Doctor')
@section('subtitle', 'Create a new doctor profile with complete professional information')

@section('actions')
<a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-2"></i>
    Back to Doctors
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-plus me-2"></i>
                    Add New Doctor
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

                <form id="doctorForm" action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-person me-2"></i>
                                Basic Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
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

                    <!-- Professional Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-briefcase me-2"></i>
                                Professional Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="professional_id" class="form-label">Professional ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('professional_id') is-invalid @enderror" id="professional_id" name="professional_id" value="{{ old('professional_id') }}" required>
                                @error('professional_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="license_scan" class="form-label">License Number</label>
                                <input type="text" class="form-control @error('license_scan') is-invalid @enderror" id="license_scan" name="license_scan" value="{{ old('license_scan') }}" placeholder="Enter license number">
                                <div class="form-text">Enter the doctor's medical license number</div>
                                @error('license_scan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror" id="specialization" name="specialization" value="{{ old('specialization') }}">
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="years_of_experience" class="form-label">Years of Experience</label>
                                <input type="number" class="form-control @error('years_of_experience') is-invalid @enderror" id="years_of_experience" name="years_of_experience" value="{{ old('years_of_experience') }}" min="0">
                                @error('years_of_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Brief description of the doctor's background and expertise...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Profile Image Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-image me-2"></i>
                                Profile Image
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
                                <label for="working_location" class="form-label">Working Location</label>
                                <input type="text" class="form-control @error('working_location') is-invalid @enderror" id="working_location" name="working_location" value="{{ old('working_location') }}" placeholder="Hospital/Clinic name">
                                @error('working_location')
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

                    <!-- Service Fees -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-currency-dollar me-2"></i>
                                Service Fees
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="consultation_fee" class="form-label">Consultation Fee ($)</label>
                                <input type="number" class="form-control @error('consultation_fee') is-invalid @enderror" id="consultation_fee" name="consultation_fee" value="{{ old('consultation_fee') }}" min="0" step="0.01" placeholder="0.00">
                                @error('consultation_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="messaging_fee" class="form-label">Messaging Fee ($)</label>
                                <input type="number" class="form-control @error('messaging_fee') is-invalid @enderror" id="messaging_fee" name="messaging_fee" value="{{ old('messaging_fee') }}" min="0" step="0.01" placeholder="0.00">
                                @error('messaging_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="video_call_fee" class="form-label">Video Call Fee ($)</label>
                                <input type="number" class="form-control @error('video_call_fee') is-invalid @enderror" id="video_call_fee" name="video_call_fee" value="{{ old('video_call_fee') }}" min="0" step="0.01" placeholder="0.00">
                                @error('video_call_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="voice_call_fee" class="form-label">Voice Call Fee ($)</label>
                                <input type="number" class="form-control @error('voice_call_fee') is-invalid @enderror" id="voice_call_fee" name="voice_call_fee" value="{{ old('voice_call_fee') }}" min="0" step="0.01" placeholder="0.00">
                                @error('voice_call_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="house_visit_fee" class="form-label">House Visit Fee ($)</label>
                                <input type="number" class="form-control @error('house_visit_fee') is-invalid @enderror" id="house_visit_fee" name="house_visit_fee" value="{{ old('house_visit_fee') }}" min="0" step="0.01" placeholder="0.00">
                                @error('house_visit_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Working Hours -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-clock me-2"></i>
                                Working Hours
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="working_hours_from" class="form-label">Working Hours From</label>
                                <input type="time" class="form-control @error('working_hours_from') is-invalid @enderror" id="working_hours_from" name="working_hours_from" value="{{ old('working_hours_from', '08:00') }}">
                                @error('working_hours_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="working_hours_to" class="form-label">Working Hours To</label>
                                <input type="time" class="form-control @error('working_hours_to') is-invalid @enderror" id="working_hours_to" name="working_hours_to" value="{{ old('working_hours_to', '17:00') }}">
                                @error('working_hours_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Working Days</label>
                                <div class="row">
                                    @php
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        $oldDays = old('working_days', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']);
                                    @endphp
                                    @foreach($days as $day)
                                        <div class="col-md-3 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="working_days[]" value="{{ $day }}" id="day_{{ $day }}" {{ in_array($day, $oldDays) ? 'checked' : '' }}>
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
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded card-content">
                                <div>
                                    <p class="mb-0 text-muted">
                                        <i class="bi bi-info-circle me-2"></i>
                                        All required fields must be filled before submission
                                    </p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitButton">
                                        <i class="bi bi-person-plus me-2"></i>
                                        Create Doctor
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
    document.getElementById('doctorForm').addEventListener('submit', function(e) {
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
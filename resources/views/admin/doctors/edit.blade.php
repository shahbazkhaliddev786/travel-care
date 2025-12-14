@extends('layouts.admin')

@section('title', 'Edit Doctor')
@section('subtitle', 'Update doctor profile and verification status')

@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Doctors
        </a>
        <a href="{{ route('admin.doctors.show', $doctor->id) }}" class="btn btn-outline-info">
            <i class="bi bi-eye me-2"></i>
            View Doctor
        </a>
    </div>
@endsection

@section('content')
<form id="doctorForm" action="{{ route('admin.doctors.update', $doctor->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row g-4">
        <!-- Basic Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-badge me-2"></i>
                        Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $doctor->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $doctor->email) }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3">
                            <label for="country_code" class="form-label">Country Code <span class="text-danger">*</span></label>
                            <select class="form-select @error('country_code') is-invalid @enderror" 
                                    id="country_code" 
                                    name="country_code" 
                                    required>
                                <option value="">Select Code</option>
                                @foreach(config('countries.country_codes') as $code => $country)
                                    <option value="{{ $code }}" {{ old('country_code', $doctor->country_code) == $code ? 'selected' : '' }}>{{ $code }} ({{ $country }})</option>
                                @endforeach
                            </select>
                            @error('country_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-9">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $doctor->phone) }}" 
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lock me-2"></i>
                        Password (Optional)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Leave password fields empty to keep current password
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control pswd-input @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="bi bi-eye" id="passwordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 8 characters required</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control pswd-input" 
                                       id="password_confirmation" 
                                       name="password_confirmation">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="bi bi-eye" id="password_confirmationIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-award me-2"></i>
                        Professional Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="professional_id" class="form-label">Professional ID <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('professional_id') is-invalid @enderror" 
                                   id="professional_id" 
                                   name="professional_id" 
                                   value="{{ old('professional_id', $doctor->professional_id) }}" 
                                   required>
                            @error('professional_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="specialization" class="form-label">Specialization</label>
                            <input type="text" 
                                   class="form-control @error('specialization') is-invalid @enderror" 
                                   id="specialization" 
                                   name="specialization" 
                                   value="{{ old('specialization', $doctor->specialization) }}">
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="years_of_experience" class="form-label">Years of Experience</label>
                            <input type="number" 
                                   class="form-control @error('years_of_experience') is-invalid @enderror" 
                                   id="years_of_experience" 
                                   name="years_of_experience" 
                                   value="{{ old('years_of_experience', $doctor->years_of_experience) }}" 
                                   min="0">
                            @error('years_of_experience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="license_scan" class="form-label">License Number</label>
                            <input type="text" 
                                   class="form-control @error('license_scan') is-invalid @enderror" 
                                   id="license_scan" 
                                   name="license_scan" 
                                   value="{{ old('license_scan', $doctor->license_scan) }}"
                                   placeholder="Enter license number">
                            @error('license_scan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the doctor's medical license number</div>
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $doctor->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-geo-alt me-2"></i>
                        Location Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $doctor->city) }}" 
                                   required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="working_location" class="form-label">Working Location</label>
                            <input type="text" 
                                   class="form-control @error('working_location') is-invalid @enderror" 
                                   id="working_location" 
                                   name="working_location" 
                                   value="{{ old('working_location', $doctor->working_location) }}">
                            @error('working_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="2" 
                                      required>{{ old('address', $doctor->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Fees -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-currency-dollar me-2"></i>
                        Service Fees
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="consultation_fee" class="form-label">Consultation Fee ($)</label>
                            <input type="number" 
                                   class="form-control @error('consultation_fee') is-invalid @enderror" 
                                   id="consultation_fee" 
                                   name="consultation_fee" 
                                   value="{{ old('consultation_fee', $doctor->consultation_fee) }}" 
                                   min="0" 
                                   step="0.01">
                            @error('consultation_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="messaging_fee" class="form-label">Messaging Fee ($)</label>
                            <input type="number" 
                                   class="form-control @error('messaging_fee') is-invalid @enderror" 
                                   id="messaging_fee" 
                                   name="messaging_fee" 
                                   value="{{ old('messaging_fee', $doctor->messaging_fee) }}" 
                                   min="0" 
                                   step="0.01">
                            @error('messaging_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="video_call_fee" class="form-label">Video Call Fee ($)</label>
                            <input type="number" 
                                   class="form-control @error('video_call_fee') is-invalid @enderror" 
                                   id="video_call_fee" 
                                   name="video_call_fee" 
                                   value="{{ old('video_call_fee', $doctor->video_call_fee) }}" 
                                   min="0" 
                                   step="0.01">
                            @error('video_call_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="voice_call_fee" class="form-label">Voice Call Fee ($)</label>
                            <input type="number" 
                                   class="form-control @error('voice_call_fee') is-invalid @enderror" 
                                   id="voice_call_fee" 
                                   name="voice_call_fee" 
                                   value="{{ old('voice_call_fee', $doctor->voice_call_fee) }}" 
                                   min="0" 
                                   step="0.01">
                            @error('voice_call_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12">
                            <label for="house_visit_fee" class="form-label">House Visit Fee ($)</label>
                            <input type="number" 
                                   class="form-control @error('house_visit_fee') is-invalid @enderror" 
                                   id="house_visit_fee" 
                                   name="house_visit_fee" 
                                   value="{{ old('house_visit_fee', $doctor->house_visit_fee) }}" 
                                   min="0" 
                                   step="0.01">
                            @error('house_visit_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Working Hours -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock me-2"></i>
                        Working Hours
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="working_hours_from" class="form-label">From</label>
                            <input type="time" 
                                   class="form-control @error('working_hours_from') is-invalid @enderror" 
                                   id="working_hours_from" 
                                   name="working_hours_from" 
                                   value="{{ old('working_hours_from', $doctor->working_hours_from) }}">
                            @error('working_hours_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="working_hours_to" class="form-label">To</label>
                            <input type="time" 
                                   class="form-control @error('working_hours_to') is-invalid @enderror" 
                                   id="working_hours_to" 
                                   name="working_hours_to" 
                                   value="{{ old('working_hours_to', $doctor->working_hours_to) }}">
                            @error('working_hours_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Working Days</label>
                            <div class="d-flex flex-wrap gap-3">
                                @php
                                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                    $workingDays = old('working_days', $doctor->working_days ?? []);
                                @endphp
                                @foreach($days as $day)
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="working_days[]" 
                                               value="{{ $day }}" 
                                               id="day_{{ strtolower($day) }}"
                                               {{ in_array($day, $workingDays) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="day_{{ strtolower($day) }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status and Settings -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Status & Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Verification Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   id="is_verified" 
                                   name="is_verified" 
                                   value="1"
                                   {{ old('is_verified', $doctor->is_verified) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_verified">
                                Verified Doctor
                            </label>
                        </div>
                        <div class="form-text">Verified doctors can accept consultations</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Video Consultation</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   id="can_video_consult" 
                                   name="can_video_consult" 
                                   value="1"
                                   {{ old('can_video_consult', $doctor->can_video_consult) ? 'checked' : '' }}>
                            <label class="form-check-label" for="can_video_consult">
                                Enable Video Consultation
                            </label>
                        </div>
                        <div class="form-text">Allows this doctor to provide video consultations</div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <small class="text-muted">Account Created</small>
                            <div>{{ $doctor->created_at->format('M d, Y \a\t H:i') }}</div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Last Updated</small>
                            <div>{{ $doctor->updated_at->format('M d, Y \a\t H:i') }}</div>
                        </div>
                        @if($doctor->rejection_reason)
                        <div class="col-12">
                            <small class="text-muted">Rejection Reason</small>
                            <div class="text-danger">{{ $doctor->rejection_reason }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Profile Image -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-image me-2"></i>
                        Profile Image
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Current Image Display -->
                    <div class="text-center mb-3">
                        <div class="profile-image-container" style="position: relative; display: inline-block;">
                            <img id="profile-image-preview" 
                                 src="{{ $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : asset('assets/icons/default-avatar.svg') }}" 
                                 alt="{{ $doctor->name }}" 
                                 class="img-fluid rounded-circle border" 
                                 style="max-width: 150px; height: 150px; object-fit: cover;">
                            @if($doctor->profile_image)
                                <button type="button" 
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle"
                                        onclick="deleteProfileImage()"
                                        title="Delete Profile Image"
                                        style="width: 30px; height: 30px; padding: 0;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Upload Section -->
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Upload New Profile Image</label>
                        <input type="file" 
                               class="form-control @error('profile_image') is-invalid @enderror" 
                               id="profile_image" 
                               name="profile_image" 
                               accept="image/*"
                               onchange="previewProfileImage(this)">
                        @error('profile_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small>Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                        </div>
                    </div>
                    
                    <!-- Delete Profile Image Hidden Field -->
                    <input type="hidden" name="delete_profile_image" id="delete_profile_image" value="0">
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button Section -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center card-content">
                <div>
                    <p class="mb-0 text-muted">
                        <i class="bi bi-info-circle me-2"></i>
                        Review changes before updating
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <i class="bi bi-check-lg me-2"></i>
                        Update Doctor
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
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

    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + 'Icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Form validation and submission handling
    document.getElementById('doctorForm').addEventListener('submit', function(e) {
        // Prevent admin layout interference
        e.stopPropagation();
        
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Password confirmation validation
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        if (password && password !== passwordConfirmation) {
            document.getElementById('password_confirmation').classList.add('is-invalid');
            isValid = false;
            alert('Password confirmation does not match.');
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fix the validation errors before submitting.');
            return;
        }

        // Show loading state
        const submitButton = document.getElementById('submitButton');
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...';
        submitButton.disabled = true;
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

    // Profile image preview function
    function previewProfileImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('profile-image-preview').src = e.target.result;
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Delete profile image function
    function deleteProfileImage() {
        if (confirm('Are you sure you want to delete the profile image?')) {
            // Set the hidden field to indicate deletion
            document.getElementById('delete_profile_image').value = '1';
            
            // Update the preview to show default avatar
            document.getElementById('profile-image-preview').src = '{{ asset('assets/icons/default-avatar.svg') }}';
            
            // Hide the delete button
            const deleteButton = document.querySelector('.profile-image-container .btn-danger');
            if (deleteButton) {
                deleteButton.style.display = 'none';
            }
            
            // Clear the file input
            document.getElementById('profile_image').value = '';
        }
    }
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
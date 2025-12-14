@extends('layouts.admin')

@section('title', 'Edit Translator')
@section('subtitle', 'Update translator profile and verification status')

@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.translators.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Translators
        </a>
        <a href="{{ route('admin.translators.show', $translator->id) }}" class="btn btn-outline-info">
            <i class="bi bi-eye me-2"></i>
            View Translator
        </a>
    </div>
@endsection

@section('content')
<form id="translatorForm" action="{{ route('admin.translators.update', $translator->id) }}" method="POST" enctype="multipart/form-data">
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
                                   value="{{ old('name', $translator->name) }}" 
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
                                   value="{{ old('email', $translator->email) }}" 
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
                                    <option value="{{ $code }}" {{ old('country_code', $translator->country_code) == $code ? 'selected' : '' }}>{{ $code }} ({{ $country }})</option>
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
                                   value="{{ old('phone', $translator->phone) }}" 
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
                        <i class="bi bi-briefcase me-2"></i>
                        Professional Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="experience_years" class="form-label">Years of Experience</label>
                            <input type="number" class="form-control @error('experience_years') is-invalid @enderror" id="experience_years" name="experience_years" value="{{ old('experience_years', $translator->experience_years) }}" min="0">
                            @error('experience_years')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label for="specialization" class="form-label">Specialization</label>
                            <input type="text" 
                                   class="form-control @error('specialization') is-invalid @enderror" 
                                   id="specialization" 
                                   name="specialization" 
                                   value="{{ old('specialization', $translator->specialization) }}" 
                                   placeholder="e.g., Medical, Legal, Technical">
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        
                        <div class="col-12">
                            <label for="bio" class="form-label">Bio/Description</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" 
                                      name="bio" 
                                      rows="3" 
                                      placeholder="Brief description of your background and expertise...">{{ old('bio', $translator->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Languages -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-globe me-2"></i>
                        Languages
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="languages" class="form-label">Languages <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('languages') is-invalid @enderror" 
                                      id="languages" 
                                      name="languages" 
                                      rows="3" 
                                      required 
                                      placeholder="List the languages you can translate (e.g., English, Spanish, French, German)">{{ old('languages', is_array($translator->languages) ? implode(', ', array_filter($translator->languages)) : (string)$translator->languages) }}</textarea>
                            @error('languages')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small>Separate multiple languages with commas</small>
                            </div>
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
                                   value="{{ old('city', $translator->city) }}" 
                                   required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" 
                                   class="form-control @error('country') is-invalid @enderror" 
                                   id="country" 
                                   name="country" 
                                   value="{{ old('country', $translator->country) }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="2" 
                                      placeholder="Complete address...">{{ old('address', $translator->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Rates -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-currency-dollar me-2"></i>
                        Service Rates
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="hourly_rate" class="form-label">Hourly Rate ($)</label>
                            <input type="number" 
                                   class="form-control @error('hourly_rate') is-invalid @enderror" 
                                   id="hourly_rate" 
                                   name="hourly_rate" 
                                   value="{{ old('hourly_rate', $translator->hourly_rate) }}" 
                                   min="0" 
                                   step="0.01" 
                                   placeholder="0.00">
                            @error('hourly_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        

                        
                        <div class="col-md-6">
                            <label for="availability_hours" class="form-label">Availability Hours</label>
                            <input type="text" 
                                   class="form-control @error('availability_hours') is-invalid @enderror" 
                                   id="availability_hours" 
                                   name="availability_hours" 
                                   value="{{ old('availability_hours', is_array($translator->availability) ? implode(', ', $translator->availability) : (string)$translator->availability) }}" 
                                   placeholder="e.g., 9 AM - 5 PM EST">
                            @error('availability_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                   {{ old('is_verified', $translator->is_verified) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_verified">
                                Verified Translator
                            </label>
                        </div>
                        <div class="form-text">Verified translators can accept translation requests</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Availability</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   id="is_available" 
                                   name="is_available" 
                                   value="1"
                                   {{ old('is_available', $translator->is_available) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_available">
                                Available for new projects
                            </label>
                        </div>
                        <div class="form-text">Available translators can receive new translation requests</div>
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
                            <div>{{ $translator->created_at->format('M d, Y \a\t H:i') }}</div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Last Updated</small>
                            <div>{{ $translator->updated_at->format('M d, Y \a\t H:i') }}</div>
                        </div>
                        @if($translator->rejection_reason)
                        <div class="col-12">
                            <small class="text-muted">Rejection Reason</small>
                            <div class="text-danger">{{ $translator->rejection_reason }}</div>
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
                                 src="{{ $translator->profile_image ? asset('storage/' . $translator->profile_image) : asset('assets/icons/default-avatar.svg') }}" 
                                 alt="{{ $translator->name }}" 
                                 class="img-fluid rounded-circle border" 
                                 style="max-width: 150px; height: 150px; object-fit: cover;">
                            @if($translator->profile_image)
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

    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center card-content">
                <div>
                    <p class="mb-0 text-muted">
                        <i class="bi bi-info-circle me-2"></i>
                        Review changes before updating
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.translators.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <i class="bi bi-check-lg me-2"></i>
                        Update Translator
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
    document.getElementById('translatorForm').addEventListener('submit', function(e) {
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
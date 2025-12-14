@extends('layouts.admin')

@section('title', 'Add New Translator')
@section('subtitle', 'Create a new translator profile with complete professional information')

@section('actions')
<a href="{{ route('admin.translators.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-2"></i>
    Back to Translators
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-translate me-2"></i>
                    Add New Translator
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

                <form id="translatorForm" action="{{ route('admin.translators.store') }}" method="POST" enctype="multipart/form-data">
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
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                <label for="experience_years" class="form-label">Years of Experience</label>
                                <input type="number" class="form-control @error('experience_years') is-invalid @enderror" id="experience_years" name="experience_years" value="{{ old('experience_years') }}" min="0">
                                @error('experience_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror" id="specialization" name="specialization" value="{{ old('specialization') }}" placeholder="e.g., Medical, Legal, Technical">
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="bio" class="form-label">Bio/Description</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3" placeholder="Brief description of your background and expertise...">{{ old('bio') }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Languages -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-globe me-2"></i>
                                Languages
                            </h6>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="languages" class="form-label">Languages <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('languages') is-invalid @enderror" id="languages" name="languages" rows="3" required placeholder="List the languages you can translate (e.g., English, Spanish, French, German)">{{ is_array(old('languages')) ? implode(', ', array_filter(old('languages'))) : old('languages') }}</textarea>
                                @error('languages')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small>Separate multiple languages with commas</small>
                                </div>
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
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country') }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Complete address...">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Service Rates -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-currency-dollar me-2"></i>
                                Service Rates
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate ($)</label>
                                <input type="number" class="form-control @error('hourly_rate') is-invalid @enderror" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}" min="0" step="0.01" placeholder="0.00">
                                @error('hourly_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        

                    </div>

                    <!-- Availability -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-clock me-2"></i>
                                Availability
                            </h6>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="availability_hours" class="form-label">Availability Hours</label>
                                <input type="text" class="form-control @error('availability_hours') is-invalid @enderror" id="availability_hours" name="availability_hours" value="{{ is_array(old('availability_hours')) ? implode(', ', array_filter(old('availability_hours'))) : old('availability_hours') }}" placeholder="e.g., 9 AM - 5 PM EST">
                                @error('availability_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available">
                                    Available for new projects
                                </label>
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
                                    <a href="{{ route('admin.translators.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitButton">
                                        <i class="bi bi-translate me-2"></i>
                                        Create Translator
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
    document.getElementById('translatorForm').addEventListener('submit', function(e) {
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
@extends('layouts.admin')

@section('title', 'Edit Customer')
@section('subtitle', 'Update customer information')

@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Customers
        </a>
        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-primary">
            <i class="bi bi-eye me-2"></i>
            View Customer
        </a>
    </div>
@endsection

@section('content')
<form id="customerForm" action="{{ route('admin.customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row g-4 container-content">
        <!-- Basic Information -->
        <div class="col-lg-8 left-col">
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
                                   value="{{ old('name', $customer->name) }}" 
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
                                   value="{{ old('email', $customer->email) }}" 
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
                                    <option value="{{ $code }}" {{ old('country_code', $customer->country_code) == $code ? 'selected' : '' }}>{{ $code }} ({{ $country }})</option>
                                @endforeach
                            </select>
                            @error('country_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-9">
                            <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number', $customer->phone_number) }}" 
                                   required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" 
                                   class="form-control @error('country') is-invalid @enderror" 
                                   id="country" 
                                   name="country" 
                                   value="{{ old('country', $customer->customerProfile->country) }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="city" class="form-label">City</label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $customer->customerProfile->city) }}">

                            @error('city')
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
                        Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Leave password fields empty if you don't want to change the password.
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
                                    <i class="bi bi-eye" id="passwordToggleIcon"></i>
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
                                       class="form-control pswd-input @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation">
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
            </div>

            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-lines-fill me-2"></i>
                        Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="biological_sex" class="form-label">Biological Sex</label>
                            <select class="form-select @error('biological_sex') is-invalid @enderror" 
                                    id="biological_sex" 
                                    name="biological_sex">
                                <option value="">Select Sex</option>
                                <option value="male" {{ old('biological_sex', strtolower($customer->customerProfile->gender ?? '')) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('biological_sex', strtolower($customer->customerProfile->gender ?? '')) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('biological_sex')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" 
                                   class="form-control @error('age') is-invalid @enderror" 
                                   id="age" 
                                   name="age" 
                                   min="1" 
                                   max="120" 
                                   value="{{ old('age', $customer->customerProfile->age) }}">
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" 
                                   class="form-control @error('weight') is-invalid @enderror" 
                                   id="weight" 
                                   name="weight" 
                                   min="0" 
                                   step="0.1" 
                                   value="{{ old('weight', $customer->customerProfile->weight) }}">

                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-heart-pulse me-2"></i>
                        Medical Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="chronic_pathologies" class="form-label">Chronic Pathologies</label>
                            <textarea class="form-control @error('chronic_pathologies') is-invalid @enderror" 
                                      id="chronic_pathologies" 
                                      name="chronic_pathologies" 
                                      rows="3" 
                                      placeholder="E.g. Diabetes, Hypertension, etc. (separate multiple entries with commas)">{{ old('chronic_pathologies', $customer->customerProfile && $customer->customerProfile->chronic_pathologies ? (is_array(json_decode($customer->customerProfile->chronic_pathologies)) ? implode(', ', json_decode($customer->customerProfile->chronic_pathologies)) : $customer->customerProfile->chronic_pathologies) : '') }}</textarea>
                            @error('chronic_pathologies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="allergies" class="form-label">Allergies</label>
                            <textarea class="form-control @error('allergies') is-invalid @enderror" 
                                      id="allergies" 
                                      name="allergies" 
                                      rows="3" 
                                      placeholder="Describe any known allergies (separate multiple entries with commas)...">{{ old('allergies', $customer->customerProfile && $customer->customerProfile->allergies ? (is_array(json_decode($customer->customerProfile->allergies)) ? implode(', ', json_decode($customer->customerProfile->allergies)) : $customer->customerProfile->allergies) : '') }}</textarea>
                            @error('allergies')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="chronic_medications" class="form-label">Chronic Medications</label>
                            <textarea class="form-control @error('chronic_medications') is-invalid @enderror" 
                                      id="chronic_medications" 
                                      name="chronic_medications" 
                                      rows="3" 
                                      placeholder="List chronic medications (separate multiple entries with commas)...">{{ old('chronic_medications', $customer->customerProfile && $customer->customerProfile->chronic_medications ? (is_array(json_decode($customer->customerProfile->chronic_medications)) ? implode(', ', json_decode($customer->customerProfile->chronic_medications)) : $customer->customerProfile->chronic_medications) : '') }}</textarea>
                            @error('chronic_medications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="medical_info" class="form-label">Additional Medical Information</label>
                            <textarea class="form-control @error('medical_info') is-invalid @enderror" 
                                      id="medical_info" 
                                      name="medical_info" 
                                      rows="4" 
                                      placeholder="Any additional medical information...">{{ old('medical_info', $customer->customerProfile->medical_info ?? '') }}</textarea>
                            @error('medical_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitButton">
                                <i class="bi bi-check-lg me-2"></i>
                                Update Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Settings -->
        <div class="col-lg-4 right-col">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-image me-2"></i>
                        Profile Photo
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="profile-photo-preview" id="profilePhotoPreview">
                            @if($customer->profile_photo)
                                <img src="{{ asset('storage/' . $customer->profile_photo) }}" 
                                     alt="Profile Photo" 
                                     style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
                            @else
                                <div class="placeholder-avatar">
                                    <i class="bi bi-person" style="font-size: 4rem; color: var(--admin-border);"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <input type="file" 
                           class="form-control @error('profile_photo') is-invalid @enderror" 
                           id="profile_photo" 
                           name="profile_photo" 
                           accept="image/*"
                           onchange="previewImage(this)">
                    @error('profile_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Upload a new profile photo (JPG, PNG, GIF - Max 2MB)
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>
                        Account Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" 
                               type="checkbox" 
                               role="switch" 
                               id="is_active" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            <strong>Active Account</strong>
                            <br>
                            <small class="text-muted">Customer can log in and use the platform</small>
                        </label>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-12">
                            <small class="text-muted">Created:</small>
                            <div>{{ $customer->customerProfile->created_at->format('M d, Y g:i A') }}</div>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted">Last Updated:</small>
                            <div>{{ $customer->customerProfile->updated_at->format('M d, Y g:i A') }}</div>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted">Customer ID:</small>
                            <div class="font-monospace">#{{ $customer->id }}</div>
                        </div>
                    </div>
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

    // Preview profile image
    function previewImage(input) {
        const preview = document.getElementById('profilePhotoPreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Profile Preview" 
                         style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
                `;
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Form validation
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const submitButton = document.getElementById('submitButton');
        
        // Check password match if password is being changed
        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            
            // Add error styling
            document.getElementById('password_confirmation').classList.add('is-invalid');
            return false;
        }

        // Show loading state
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
        submitButton.disabled = true;
        
        return true;
    });

    // Real-time password confirmation validation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (password && confirmPassword && password !== confirmPassword) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
</script>

<style>
    .profile-photo-preview {
        width: 120px;
        height: 120px;
        margin: 0 auto;
        border-radius: 50%;
        border: 3px dashed var(--admin-border);
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--admin-light);
    }
    
    .placeholder-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
</style>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
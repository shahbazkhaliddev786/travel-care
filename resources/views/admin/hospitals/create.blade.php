@extends('layouts.admin')

@section('title', 'Add New Hospital')
@section('subtitle', 'Create a new hospital profile with complete information')

@section('actions')
<a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-2"></i>
    Back to Hospitals
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-hospital me-2"></i>
                    Add New Hospital
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

                <form id="hospitalForm" action="{{ route('admin.hospitals.store') }}" method="POST" enctype="multipart/form-data">
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
                                <label for="name" class="form-label">Hospital Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="type" class="form-label">Hospital Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General Hospital</option>
                                    <option value="specialized" {{ old('type') == 'specialized' ? 'selected' : '' }}>Specialized Hospital</option>
                                    <option value="clinic" {{ old('type') == 'clinic' ? 'selected' : '' }}>Clinic</option>
                                    <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>Emergency Center</option>
                                    <option value="rehabilitation" {{ old('type') == 'rehabilitation' ? 'selected' : '' }}>Rehabilitation Center</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
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
                        
                        <div class="col-md-3">
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
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="logo" class="form-label">Hospital Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                <div class="form-text">Accepted formats: JPG, JPEG, PNG. Max size: 2MB</div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of the hospital...">{{ old('description') }}</textarea>
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
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="country" name="country" value="{{ old('country', 'Mexico') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="state" class="form-label">State/Province</label>
                                <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="2" required placeholder="Complete address...">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Services & Facilities -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-gear me-2"></i>
                                Services & Facilities
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="specialties" class="form-label">Medical Specialties</label>
                                <textarea class="form-control" id="specialties" name="specialties" rows="3" placeholder="List the medical specialties available (e.g. Cardiology, Neurology, Pediatrics...)">{{ old('specialties') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="facilities" class="form-label">Facilities & Equipment</label>
                                <textarea class="form-control" id="facilities" name="facilities" rows="3" placeholder="List available facilities (e.g. ICU, Emergency Room, Laboratory...)">{{ old('facilities') }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="bed_count" class="form-label">Total Beds</label>
                                <input type="number" class="form-control" id="bed_count" name="bed_count" value="{{ old('bed_count') }}" min="0">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="emergency_services" class="form-label">Emergency Services</label>
                                <select class="form-select" id="emergency_services" name="emergency_services">
                                    <option value="1" {{ old('emergency_services') == '1' ? 'selected' : '' }}>Available 24/7</option>
                                    <option value="0" {{ old('emergency_services') == '0' ? 'selected' : '' }}>Not Available</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="pharmacy" class="form-label">Pharmacy</label>
                                <select class="form-select" id="pharmacy" name="pharmacy">
                                    <option value="1" {{ old('pharmacy') == '1' ? 'selected' : '' }}>Available</option>
                                    <option value="0" {{ old('pharmacy') == '0' ? 'selected' : '' }}>Not Available</option>
                                </select>
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
                                <label for="operating_hours_from" class="form-label">Open From</label>
                                <input type="time" class="form-control" id="operating_hours_from" name="operating_hours_from" value="{{ old('operating_hours_from', '06:00') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="operating_hours_to" class="form-label">Open Until</label>
                                <input type="time" class="form-control" id="operating_hours_to" name="operating_hours_to" value="{{ old('operating_hours_to', '22:00') }}">
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label">Operating Days</label>
                                <div class="row">
                                    @php
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        $oldDays = old('operating_days', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
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
                            <a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitButton">
                                <i class="bi bi-hospital me-2"></i>
                                Create Hospital
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

    // Form validation
    document.getElementById('hospitalForm').addEventListener('submit', function(e) {
        const submitButton = document.getElementById('submitButton');
        
        // Show loading state
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating...';
        submitButton.disabled = true;
        
        return true;
    });
</script>
@endsection

@section('scripts')
<script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        // Logo preview
        const logoInput = document.getElementById('logo');
        if (logoInput) {
            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        this.value = '';
                        return;
                    }
                    
                    // Validate file type
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPG, JPEG, PNG)');
                        this.value = '';
                        return;
                    }
                }
            });
        }
        
        // Form submission validation
        form.addEventListener('submit', function(e) {
            const requiredFields = ['name', 'type', 'email', 'country_code', 'phone', 'country', 'city', 'address'];
            let isValid = true;
            
            requiredFields.forEach(function(fieldName) {
                const field = document.getElementById(fieldName);
                if (field && !field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else if (field) {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
        
        // Remove invalid class on input
        document.querySelectorAll('.form-control, .form-select').forEach(function(field) {
            field.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
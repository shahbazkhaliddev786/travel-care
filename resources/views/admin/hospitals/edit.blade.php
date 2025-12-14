@extends('layouts.admin')

@section('title', 'Edit Hospital')
@section('subtitle', 'Update hospital information and settings')

@section('actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Hospitals
    </a>
    <a href="{{ route('admin.hospitals.show', $hospital->id) }}" class="btn btn-outline-info">
        <i class="bi bi-eye me-2"></i>
        View Hospital
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-hospital me-2"></i>
                    Edit Hospital: {{ $hospital->name }}
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

                <form action="{{ route('admin.hospitals.update', $hospital->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
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
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $hospital->name) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="type" class="form-label">Hospital Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="general" {{ old('type', $hospital->type) == 'general' ? 'selected' : '' }}>General Hospital</option>
                                    <option value="specialized" {{ old('type', $hospital->type) == 'specialized' ? 'selected' : '' }}>Specialized Hospital</option>
                                    <option value="clinic" {{ old('type', $hospital->type) == 'clinic' ? 'selected' : '' }}>Clinic</option>
                                    <option value="emergency" {{ old('type', $hospital->type) == 'emergency' ? 'selected' : '' }}>Emergency Center</option>
                                    <option value="rehabilitation" {{ old('type', $hospital->type) == 'rehabilitation' ? 'selected' : '' }}>Rehabilitation Center</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $hospital->email) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="country_code" class="form-label">Country Code <span class="text-danger">*</span></label>
                                <select class="form-select @error('country_code') is-invalid @enderror" id="country_code" name="country_code" required>
                                    <option value="">Select Code</option>
                                    @foreach(config('countries.country_codes') as $code => $country)
                                        <option value="{{ $code }}" {{ old('country_code', $hospital->country_code) == $code ? 'selected' : '' }}>{{ $code }} ({{ $country }})</option>
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
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $hospital->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $hospital->website) }}" placeholder="https://example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of the hospital...">{{ old('description', $hospital->description) }}</textarea>
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
                                <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $hospital->country ?? 'Mexico') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $hospital->city) }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="state" class="form-label">State/Province</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state', $hospital->state) }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $hospital->postal_code) }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="2" required placeholder="Complete address...">{{ old('address', $hospital->address) }}</textarea>
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
                                <textarea class="form-control" id="specialties" name="specialties" rows="3" placeholder="List the medical specialties available (e.g. Cardiology, Neurology, Pediatrics...)">{{ old('specialties', $hospital->specialties) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="facilities" class="form-label">Facilities & Equipment</label>
                                <textarea class="form-control" id="facilities" name="facilities" rows="3" placeholder="List available facilities (e.g. ICU, Emergency Room, Laboratory...)">{{ old('facilities', $hospital->facilities) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="bed_count" class="form-label">Total Beds</label>
                                <input type="number" class="form-control" id="bed_count" name="bed_count" value="{{ old('bed_count', $hospital->bed_count) }}" min="0">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="emergency_services" class="form-label">Emergency Services</label>
                                <select class="form-select @error('emergency_services') is-invalid @enderror" id="emergency_services" name="emergency_services">
                                    <option value="1" {{ old('emergency_services', $hospital->emergency_services) == '1' ? 'selected' : '' }}>Available 24/7</option>
                                    <option value="0" {{ old('emergency_services', $hospital->emergency_services) == '0' ? 'selected' : '' }}>Not Available</option>
                                </select>
                                @error('emergency_services')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="pharmacy" class="form-label">Pharmacy</label>
                                <select class="form-select @error('pharmacy') is-invalid @enderror" id="pharmacy" name="pharmacy">
                                    <option value="1" {{ old('pharmacy', $hospital->pharmacy) == '1' ? 'selected' : '' }}>Available</option>
                                    <option value="0" {{ old('pharmacy', $hospital->pharmacy) == '0' ? 'selected' : '' }}>Not Available</option>
                                </select>
                                @error('pharmacy')
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
                                <label for="operating_hours_from" class="form-label">Open From</label>
                                <input type="time" class="form-control @error('operating_hours_from') is-invalid @enderror" id="operating_hours_from" name="operating_hours_from" value="{{ old('operating_hours_from', $hospital->operating_hours_from ?? '06:00') }}">
                                @error('operating_hours_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="operating_hours_to" class="form-label">Open Until</label>
                                <input type="time" class="form-control @error('operating_hours_to') is-invalid @enderror" id="operating_hours_to" name="operating_hours_to" value="{{ old('operating_hours_to', $hospital->operating_hours_to ?? '22:00') }}">
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
                                        $hospitalDays = $hospital->operating_days ? json_decode($hospital->operating_days, true) : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        $oldDays = old('operating_days', $hospitalDays);
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
                                Review changes before updating
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitButton">
                                <i class="bi bi-check-lg me-2"></i>
                                Update Hospital
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
    document.querySelector('form').addEventListener('submit', function(e) {
        const submitButton = document.getElementById('submitButton');
        
        // Show loading state
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
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
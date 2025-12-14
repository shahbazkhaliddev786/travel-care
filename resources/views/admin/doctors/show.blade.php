@extends('layouts.admin')

@section('title', 'Doctor Details')

@section('actions')
<a href="{{ route('admin.doctors.index') }}" class="btn btn-sm btn-secondary">
    <i class="bi bi-arrow-left"></i> Back to Doctors
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Doctor Profile Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Doctor Profile</h6>
                <div>
                    @if($doctor->is_verified === false && is_null($doctor->rejection_reason))
                        <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $doctor->id }}, 'approve')">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus({{ $doctor->id }}, 'reject')">
                            <i class="bi bi-x-lg"></i> Reject
                        </button>
                    @elseif($doctor->is_verified === false && !is_null($doctor->rejection_reason))
                        <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $doctor->id }}, 'approve')">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                    @elseif($doctor->is_verified === true)
                        <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus({{ $doctor->id }}, 'reject')">
                            <i class="bi bi-x-lg"></i> Reject
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($doctor->profile_image && file_exists(public_path('storage/' . $doctor->profile_image)))
                        <img src="{{ asset('storage/' . $doctor->profile_image) }}" alt="{{ $doctor->name }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white mx-auto" 
                             style="width: 150px; height: 150px; font-size: 4rem; font-weight: 600;">
                            {{ substr($doctor->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <h5 class="card-title">{{ $doctor->name }}</h5>
                <p class="card-text">
                    @if($doctor->is_verified === true)
                        <span class="badge bg-success">Approved</span>
                    @elseif($doctor->is_verified === false && is_null($doctor->rejection_reason))
                        <span class="badge bg-warning">Pending</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </p>
                <p class="card-text">
                    <i class="bi bi-envelope"></i> {{ $doctor->email }}<br>
                    <i class="bi bi-telephone"></i> {{ $doctor->country_code }} {{ $doctor->phone }}<br>
                    <i class="bi bi-geo-alt"></i> {{ $doctor->city }}@if($doctor->address), {{ $doctor->address }}@endif
                </p>
                <p class="card-text">
                    <small class="text-muted">Registered on {{ $doctor->created_at->format('M d, Y') }}</small>
                </p>
                
                <!-- Video Consultation Toggle -->
                <div class="form-check form-switch d-flex justify-content-center align-items-center mt-3">
                    <input class="form-check-input me-2" type="checkbox" role="switch" id="videoConsultation" 
                        {{ $doctor->can_video_consult ? 'checked' : '' }}
                        onchange="toggleVideoConsultation({{ $doctor->id }}, this.checked)">
                    <label class="form-check-label" for="videoConsultation">
                        Video Consultation {{ $doctor->can_video_consult ? 'Enabled' : 'Disabled' }}
                    </label>
                </div>
            </div>
        </div>

        <!-- License Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">License Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Professional ID:</strong> {{ $doctor->professional_id ?? 'Not provided' }}</p>
                <p><strong>License Number:</strong> {{ $doctor->license_scan ?? 'Not provided' }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Professional Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Professional Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Type:</strong> {{ $doctor->type ?? 'Doctor' }}</p>
                        <p><strong>Consultation Fee:</strong> ${{ $doctor->consultation_fee ?? 'Not set' }}</p>
                        <p><strong>Specialization:</strong> {{ $doctor->specialization ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Years of Experience:</strong> {{ $doctor->years_of_experience ?? 'Not specified' }}</p>
                        <p><strong>Working Location:</strong> {{ $doctor->working_location ?? 'Not specified' }}</p>
                        <p><strong>Working Hours:</strong> {{ $doctor->working_hours_from ?? '08:00' }} - {{ $doctor->working_hours_to ?? '17:00' }}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Description</h6>
                        <p>{{ $doctor->description ?? 'No description provided' }}</p>
                    </div>
                </div>
                
                @if($doctor->working_days)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Working Days</h6>
                            <div>
                                @foreach($doctor->working_days as $day)
                                    <span class="badge bg-secondary me-1">{{ ucfirst($day) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Fees Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Service Fees</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Messaging Fee:</strong> ${{ $doctor->messaging_fee ?? 'Not set' }}</p>
                        <p><strong>Voice Call Fee:</strong> ${{ $doctor->voice_call_fee ?? 'Not set' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Video Call Fee:</strong> ${{ $doctor->video_call_fee ?? 'Not set' }}</p>
                        <p><strong>House Visit Fee:</strong> ${{ $doctor->house_visit_fee ?? 'Not set' }}</p>
                    </div>
                </div>
                @if($doctor->paypal_email)
                    <p><strong>PayPal Email:</strong> {{ $doctor->paypal_email }}</p>
                @endif
            </div>
        </div>

        <!-- Services -->
        @if($doctor->services && count($doctor->services) > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Services Offered</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($doctor->services as $service)
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">{{ $service->name }}</h6>
                                <p class="card-text">{{ $service->description }}</p>
                                @if($service->price)
                                    <p class="card-text"><strong>Price:</strong> ${{ $service->price }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Rejection Reason (if rejected) -->
        @if($doctor->rejection_reason)
        <div class="card shadow mb-4 border-danger overflow-hidden">
            <div class="p-3 bg-danger text-white">
                <h6 class="m-0 font-weight-bold">Rejection Reason</h6>
            </div>
            <div class="card-body">
                <p class="text-danger">{{ $doctor->rejection_reason }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
    <div class="modal-dialog w-50">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectionModalLabel">Reject Doctor Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectionForm">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejectionReason" rows="4" placeholder="Please provide a detailed reason for rejecting this doctor's application..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRejection()">Reject Doctor</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Update doctor status (approve/reject)
    let currentDoctorId = null;
    
    function updateStatus(doctorId, status) {
        if (status === 'reject') {
            // Show rejection modal
            currentDoctorId = doctorId;
            $('#rejectionModal').modal('show');
            return;
        }
        
        if (!confirm('Are you sure you want to approve this doctor?')) {
            return;
        }
        
        performStatusUpdate(doctorId, status);
    }
    
    function confirmRejection() {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) {
            alert('Please provide a reason for rejection.');
            return;
        }
        
        $('#rejectionModal').modal('hide');
        performStatusUpdate(currentDoctorId, 'reject', reason);
    }
    
    function performStatusUpdate(doctorId, status, rejectionReason = null) {
        const data = { status: status };
        if (rejectionReason) {
            data.rejection_reason = rejectionReason;
        }
        
        fetch(`{{ url('admin/doctors') }}/${doctorId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to update status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status');
        });
    }
    
    // Toggle video consultation access
    function toggleVideoConsultation(doctorId, enabled) {
        fetch(`{{ url('admin/doctors') }}/${doctorId}/toggle-video`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ enabled: enabled })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success notification could be added here
                console.log('Video consultation updated successfully');
            } else {
                alert('Failed to update video consultation access: ' + (data.message || 'Unknown error'));
                // Reset the toggle to its previous state
                document.getElementById('videoConsultation').checked = !enabled;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating video consultation access');
            // Reset the toggle to its previous state
            document.getElementById('videoConsultation').checked = !enabled;
        });
    }
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
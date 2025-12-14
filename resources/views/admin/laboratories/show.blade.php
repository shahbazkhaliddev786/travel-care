@extends('layouts.admin')

@section('title', 'Laboratory Details')

@section('actions')
<a href="{{ route('admin.laboratories.index') }}" class="btn btn-sm btn-secondary">
    <i class="bi bi-arrow-left"></i> Back to Laboratories
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Laboratory Profile Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Laboratory Profile</h6>
                <div>
                    @if(!$laboratory->is_verified && is_null($laboratory->rejection_reason))
                        <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $laboratory->id }}, 'approve')">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus({{ $laboratory->id }}, 'reject')">
                            <i class="bi bi-x-lg"></i> Reject
                        </button>
                    @elseif(!$laboratory->is_verified && !is_null($laboratory->rejection_reason))
                        <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $laboratory->id }}, 'approve')">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                    @elseif($laboratory->is_verified && is_null($laboratory->rejection_reason))
                        <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus({{ $laboratory->id }}, 'reject')">
                            <i class="bi bi-x-lg"></i> Reject
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($laboratory->profile_image && file_exists(public_path('storage/' . $laboratory->profile_image)))
                        <img src="{{ asset('storage/' . $laboratory->profile_image) }}" alt="{{ $laboratory->name }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white mx-auto" 
                             style="width: 150px; height: 150px; font-size: 4rem; font-weight: 600;">
                            {{ substr($laboratory->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <h5 class="card-title">{{ $laboratory->name }}</h5>
                <p class="card-text">
                    @if($laboratory->is_verified && is_null($laboratory->rejection_reason))
                        <span class="badge bg-success">Approved</span>
                    @elseif(!$laboratory->is_verified && is_null($laboratory->rejection_reason))
                        <span class="badge bg-warning">Pending</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </p>
                <p class="card-text">
                    <i class="bi bi-envelope"></i> {{ $laboratory->user->email }}<br>
                    @if($laboratory->phone)
                        <i class="bi bi-telephone"></i> {{ $laboratory->phone }}<br>
                    @endif
                    @if($laboratory->city)
                        <i class="bi bi-geo-alt"></i> {{ $laboratory->city }}@if($laboratory->address), {{ $laboratory->address }}@endif
                    @endif
                </p>
                <p class="card-text">
                    <small class="text-muted">Registered on {{ $laboratory->user->created_at->format('M d, Y') }}</small>
                </p>
                
                <!-- Video Consultation Toggle -->
                <div class="form-check form-switch d-flex justify-content-center align-items-center mt-3">
                    <input class="form-check-input me-2" type="checkbox" role="switch" id="videoConsultation"
{{ $laboratory->can_video_consult ? 'checked' : '' }}
onchange="toggleVideoConsultation({{ $laboratory->id }}, this.checked)">
<label class="form-check-label" for="videoConsultation">
Video Consultation {{ $laboratory->can_video_consult ? 'Enabled' : 'Disabled' }}
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
                <p><strong>License Number:</strong> {{ $laboratory->license_number ?? 'Not provided' }}</p>
                @if($laboratory->accreditation)
                    <p><strong>Accreditation:</strong> {{ $laboratory->accreditation }}</p>
                @endif
                @if($laboratory->certification_documents)
                    <p><strong>Certification Documents:</strong> Available</p>
                @endif
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
                        <p><strong>Type:</strong> {{ $laboratory->type ?? 'Laboratory' }}</p>
                        <p><strong>Consultation Fee:</strong> ${{ $laboratory->consultation_fee ?? 'Not set' }}</p>
                        <p><strong>Specialization:</strong> {{ $laboratory->specialization ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Years of Experience:</strong> {{ $laboratory->years_of_experience ?? 'Not specified' }}</p>
                        <p><strong>Working Location:</strong> {{ $laboratory->working_location ?? 'Not specified' }}</p>
                        <p><strong>Working Hours:</strong> {{ $laboratory->operating_hours_from ?? ($laboratory->working_hours_from ?? '08:00') }} - {{ $laboratory->operating_hours_to ?? ($laboratory->working_hours_to ?? '17:00') }}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Bio/Description</h6>
                        <p>{{ $laboratory->bio ?? $laboratory->description ?? 'No description provided' }}</p>
                    </div>
                </div>
                
                @if($laboratory->operating_days)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Working Days</h6>
                            <div>
                                @php
                                    $days = is_array($laboratory->operating_days) ? $laboratory->operating_days : json_decode($laboratory->operating_days, true);
                                @endphp
                                @if($days)
                                    @foreach($days as $day)
                                        <span class="badge bg-secondary me-1">{{ ucfirst($day) }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Service Fees -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Service Fees</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Messaging Fee:</strong> ${{ $laboratory->messaging_fee ?? 'Not set' }}</p>
                        <p><strong>Voice Call Fee:</strong> ${{ $laboratory->voice_call_fee ?? 'Not set' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Video Call Fee:</strong> ${{ $laboratory->video_call_fee ?? 'Not set' }}</p>
                        <p><strong>House Visit Fee:</strong> ${{ $laboratory->house_visit_fee ?? 'Not set' }}</p>
                    </div>
                </div>
                @if($laboratory->paypal_email)
                    <p><strong>PayPal Email:</strong> {{ $laboratory->paypal_email }}</p>
                @endif
            </div>
        </div>

        <!-- Services -->
        @if($laboratory->services && count($laboratory->services) > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Services Offered</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($laboratory->services as $service)
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

        <!-- Payment Methods -->
        @if($laboratory->paymentMethods && count($laboratory->paymentMethods) > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Payment Methods</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($laboratory->paymentMethods as $method)
                    <div class="col-md-6 mb-2">
                        <span class="badge bg-info">{{ $method->method_name }}</span>
                        @if($method->details)
                            <small class="text-muted"> - {{ $method->details }}</small>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Gallery Images -->
        @if($laboratory->galleryImages && count($laboratory->galleryImages) > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Gallery</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($laboratory->galleryImages as $image)
                    <div class="col-md-3 mb-3">
                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gallery Image" class="img-fluid rounded" style="height: 150px; object-fit: cover; width: 100%;">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Reviews -->
        @if($laboratory->reviews && count($laboratory->reviews) > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Recent Reviews ({{ $laboratory->reviews->count() }} total)</h6>
            </div>
            <div class="card-body">
                @foreach($laboratory->reviews->take(5) as $review)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ $review->user->name ?? 'Anonymous' }}</strong>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                                <span class="text-muted">({{ $review->rating }}/5)</span>
                            </div>
                        </div>
                        <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                    </div>
                    @if($review->comment)
                        <p class="mt-2 mb-0">{{ $review->comment }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Rejection Reason (if rejected) -->
        @if($laboratory->rejection_reason)
        <div class="card shadow mb-4 border-danger overflow-hidden">
            <div class="p-3 bg-danger text-white">
                <h6 class="m-0 font-weight-bold">Rejection Reason</h6>
            </div>
            <div class="card-body">
                <p class="text-danger">{{ $laboratory->rejection_reason }}</p>
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
                <h5 class="modal-title" id="rejectionModalLabel">Reject Laboratory Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectionForm">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejectionReason" rows="4" placeholder="Please provide a detailed reason for rejecting this laboratory's application..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRejection()">Reject Laboratory</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Update laboratory status (approve/reject)
    let currentLaboratoryId = null;
    
    function updateStatus(laboratoryId, status) {
        if (status === 'reject') {
            // Show rejection modal (jQuery) to match Doctors page behavior
            currentLaboratoryId = laboratoryId;
            $('#rejectionModal').modal('show');
            return;
        }
        
        if (!confirm('Are you sure you want to approve this laboratory?')) {
            return;
        }
        
        performStatusUpdate(laboratoryId, status);
    }
    
    function confirmRejection() {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) {
            alert('Please provide a reason for rejection.');
            return;
        }
        
        // Hide modal via jQuery to align with Doctors page
        $('#rejectionModal').modal('hide');
        performStatusUpdate(currentLaboratoryId, 'reject', reason);
    }
    
    function performStatusUpdate(laboratoryId, status, rejectionReason = null) {
        const payload = { status };
        if (rejectionReason) {
            payload.rejection_reason = rejectionReason;
        }
        
        fetch(`{{ url('admin/laboratories') }}/${laboratoryId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(async (response) => {
            const contentType = response.headers.get('content-type') || '';
            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }
            if (contentType.includes('application/json')) {
                return response.json();
            }
            const text = await response.text();
            // Attempt to extract message from non-JSON response
            return { success: response.ok, message: text };
        })
        .then(data => {
            if (data && data.success) {
                window.location.reload();
            } else {
                alert('Failed to update status: ' + (data && data.message ? data.message : 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status');
        });
    }
    
    // Toggle video consultation access (unchanged)
    function toggleVideoConsultation(laboratoryId, enabled) {
        fetch(`{{ url('admin/laboratories') }}/${laboratoryId}/toggle-video`, {
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
                console.log('Video consultation updated successfully');
            } else {
                alert('Failed to update video consultation access: ' + (data.message || 'Unknown error'));
                document.getElementById('videoConsultation').checked = !enabled;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating video consultation access');
            document.getElementById('videoConsultation').checked = !enabled;
        });
    }
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
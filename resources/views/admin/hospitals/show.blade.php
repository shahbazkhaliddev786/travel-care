@extends('layouts.admin')

@section('title', 'Hospital Details')

@section('actions')
<a href="{{ route('admin.hospitals.index') }}" class="btn btn-sm btn-secondary">
    <i class="bi bi-arrow-left"></i> Back to List
</a>
@endsection

@section('content')
<div class="row">
    <!-- Hospital Profile Information -->
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Hospital Profile</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-12 text-center mb-4 mb-md-0">
                        @if($hospital->logo)
                            <img src="{{ asset('storage/' . $hospital->logo) }}" alt="{{ $hospital->name }}" class="img-fluid rounded mb-3" style="max-height: 150px;">
                        @else
                            <div class="rounded bg-secondary d-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px; margin: 0 auto;">
                                <span class="text-white display-4">{{ substr($hospital->name, 0, 1) }}</span>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            @if($hospital->is_verified === 1)
                                <span class="badge bg-success">Approved</span>
                            @elseif($hospital->is_verified === 0)
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </div>
                        
                        @if($hospital->is_verified === 0)
                            <div class="btn-group-vertical d-xl-none mb-3">
                                <button type="button" class="btn btn-sm btn-success mb-2" onclick="updateStatus({{ $hospital->id }}, 1)">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus({{ $hospital->id }}, -1)">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            </div>
                            <div class="btn-group d-none d-xl-flex mb-3">
                                <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $hospital->id }}, 1)">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus({{ $hospital->id }}, -1)">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            </div>
                        @elseif($hospital->is_verified === -1)
                            <button type="button" class="btn btn-sm btn-success mb-3 w-100 w-xl-auto" onclick="updateStatus({{ $hospital->id }}, 1)">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                        @elseif($hospital->is_verified === 1)
                            <button type="button" class="btn btn-sm btn-danger mb-3 w-100 w-xl-auto" onclick="updateStatus({{ $hospital->id }}, -1)">
                                <i class="bi bi-x-lg"></i> Reject
                            </button>
                        @endif
                    </div>
                    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
                        <h4>{{ $hospital->name }}</h4>
                        <p class="text-muted">Registered on {{ $hospital->created_at->format('M d, Y') }}</p>
                        
                        <div class="row mb-3">
                            <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                                <h6>Contact Information</h6>
                                <p><i class="bi bi-envelope me-2"></i> {{ $hospital->email }}</p>
                                <p><i class="bi bi-telephone me-2"></i> {{ $hospital->country_code }} {{ $hospital->phone }}</p>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <h6>Location</h6>
                                <p><i class="bi bi-geo-alt me-2"></i> {{ $hospital->city }}, {{ $hospital->address }}</p>
                            </div>
                        </div>
                        
                        @if($hospital->description)
                            <div class="mb-3">
                                <h6>Description</h6>
                                <p>{{ $hospital->description }}</p>
                            </div>
                        @endif
                        
                        @if($hospital->professional_id)
                            <div class="mb-3">
                                <h6>Professional ID</h6>
                                <p>{{ $hospital->professional_id }}</p>
                            </div>
                        @endif
                        
                        @if($hospital->license_scan)
                            <div class="mb-3">
                                <h6>License</h6>
                                <a href="{{ asset('storage/' . $hospital->license_scan) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark"></i> View License
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Hospital Details -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Hospital Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($hospital->type)
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <h6>Hospital Type</h6>
                            <p>{{ ucfirst($hospital->type) }}</p>
                        </div>
                    @endif
                    @if($hospital->specialties)
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <h6>Specialties</h6>
                            <p>{{ $hospital->specialties }}</p>
                        </div>
                    @endif
                    @if($hospital->facilities)
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <h6>Facilities</h6>
                            <p>{{ $hospital->facilities }}</p>
                        </div>
                    @endif
                    @if($hospital->bed_count)
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <h6>Total Beds</h6>
                            <p>{{ $hospital->bed_count }}</p>
                        </div>
                    @endif
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <h6>Emergency Services</h6>
                        <p>{{ $hospital->emergency_services ? 'Available 24/7' : 'Not Available' }}</p>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                        <h6>Pharmacy</h6>
                        <p>{{ $hospital->pharmacy ? 'Available' : 'Not Available' }}</p>
                    </div>
                    @if($hospital->operating_hours_from && $hospital->operating_hours_to)
                        <div class="col-12 mb-3">
                            <h6>Operating Hours</h6>
                            <p>{{ $hospital->operating_hours_from }} - {{ $hospital->operating_hours_to }}</p>
                            @if($hospital->operating_days)
                                @php
                                    $days = json_decode($hospital->operating_days, true);
                                @endphp
                                @if($days)
                                    <p><small class="text-muted">{{ implode(', ', $days) }}</small></p>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle modal form submissions
        const modalForms = document.querySelectorAll('.modal form');
        
        modalForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                    submitBtn.disabled = true;
                    
                    // Re-enable button after form submission completes (fallback)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 5000);
                }
            });
        });
    });

    // Update hospital status (approve/reject)
    function updateStatus(hospitalId, status) {
        let confirmMessage = '';
        if (status === 1) {
            confirmMessage = 'Are you sure you want to approve this hospital?';
        } else if (status === -1) {
            confirmMessage = 'Are you sure you want to reject this hospital?';
        }
        
        if (!confirm(confirmMessage)) {
            return;
        }
        
        fetch(`{{ url('admin/hospitals') }}/${hospitalId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
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
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
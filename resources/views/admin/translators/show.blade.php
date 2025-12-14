@extends('layouts.admin')

@section('title', 'Translator Details')

@section('actions')
<a href="{{ route('admin.translators.index') }}" class="btn btn-sm btn-secondary">
    <i class="bi bi-arrow-left"></i> Back to Translators
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Translator Profile Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Translator Profile</h6>
                <div>
                    @php
                        $isApproved = (bool)($translator->is_verified ?? false);
                        $isRejected = !$isApproved && !empty($translator->rejection_reason);
                        $isPending = !$isApproved && empty($translator->rejection_reason);
                    @endphp
                    @if($isPending)
                        <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $translator->id }}, 'approve')">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus({{ $translator->id }}, 'reject')">
                            <i class="bi bi-x-lg"></i> Reject
                        </button>
                    @elseif($isRejected)
                        <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $translator->id }}, 'approve')">
                            <i class="bi bi-check-lg"></i> Approve
                        </button>
                    @elseif($isApproved)
                        <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus({{ $translator->id }}, 'reject')">
                            <i class="bi bi-x-lg"></i> Reject
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @php $photo = $translator->profile_image ?? $translator->profile_photo; @endphp
                    @if($photo && file_exists(public_path('storage/' . $photo)))
                        <img src="{{ asset('storage/' . $photo) }}" alt="{{ $translator->name }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white mx-auto" 
                             style="width: 150px; height: 150px; font-size: 4rem; font-weight: 600;">
                            <i class="bi bi-translate"></i>
                        </div>
                    @endif
                </div>
                <h5 class="card-title">{{ $translator->name }}</h5>
                <p class="card-text">
                    @php
                        $statusLabel = $isApproved ? 'approved' : ($isRejected ? 'rejected' : 'pending');
                    @endphp
                    @if($statusLabel === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($statusLabel === 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @else
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                    @if(isset($translator->is_verified) && $translator->is_verified)
                        <span class="badge bg-info ms-1">Verified</span>
                    @endif
                </p>
                <p class="card-text">
                    <i class="bi bi-envelope"></i> {{ $translator->email ?? ($translator->user->email ?? 'N/A') }}<br>
                    @if($translator->phone)
                        <i class="bi bi-telephone"></i> {{ isset($translator->country_code) ? ('+' . $translator->country_code . ' ') : '' }}{{ $translator->phone }}<br>
                    @endif
                    @if($translator->city || $translator->country || $translator->address)
                        <i class="bi bi-geo-alt"></i>
                        {{ $translator->city }}
                        @if($translator->country)
                            , {{ $translator->country }}
                        @endif
                        @if($translator->address)
                            , {{ $translator->address }}
                        @endif
                    @endif
                </p>
                <p class="card-text">
                    <small class="text-muted">Registered on {{ optional($translator->user->created_at)->format('M d, Y') }}</small>
                </p>
                
                <!-- Availability Toggle -->
                <div class="form-check form-switch d-flex justify-content-center align-items-center mt-3">
                    <input class="form-check-input me-2" type="checkbox" role="switch" id="availability" 
                        {{ $translator->is_available ? 'checked' : '' }}
                        onchange="toggleAvailability({{ $translator->id }}, this.checked)">
                    <label class="form-check-label" for="availability">
                        {{ $translator->is_available ? 'Available' : 'Unavailable' }}
                    </label>
                </div>
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
                        @if($translator->experience_years)
                            <p><strong>Years of Experience:</strong> {{ $translator->experience_years }}</p>
                        @endif
                        @if($translator->specializations)
                            @php $specials = is_array($translator->specializations) ? $translator->specializations : json_decode($translator->specializations, true); @endphp
                            @if($specials && count($specials) > 0)
                                <p><strong>Specializations:</strong></p>
                                <div>
                                    @foreach($specials as $spec)
                                        <span class="badge bg-secondary me-1">{{ $spec }}</span>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($translator->hourly_rate)
                            <p><strong>Hourly Rate:</strong> ${{ $translator->hourly_rate }} per hour</p>
                        @endif
                        @if($translator->availability)
                            <p><strong>Availability Hours:</strong> {{ is_array($translator->availability) ? implode(', ', $translator->availability) : (string)$translator->availability }}</p>
                        @endif
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Bio/Description</h6>
                        <p>{{ $translator->bio ?? 'No description provided' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Performance</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Rating:</strong> {{ $translator->rating !== null ? number_format($translator->rating, 2) : 'N/A' }}</p>
                        <p><strong>Total Jobs:</strong> {{ $translator->total_jobs ?? 0 }}</p>
                    </div>
                    <div class="col-md-6">
                        @if(isset($translator->is_verified) && $translator->is_verified)
                            <p><strong>Verified:</strong> Yes <small class="text-muted">({{ $translator->verification_date ? $translator->verification_date->format('M d, Y') : '' }})</small></p>
                        @else
                            <p><strong>Verified:</strong> No</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Languages -->
        @if($translator->languages)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Languages</h6>
            </div>
            <div class="card-body">
                @php
                    $languages = is_array($translator->languages) ? $translator->languages : json_decode($translator->languages, true);
                @endphp
                @if($languages && count($languages) > 0)
                    <div class="row">
                        @foreach($languages as $language)
                        <div class="col-md-4 mb-2">
                            <span class="badge bg-primary">{{ $language }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No languages specified</p>
                @endif
            </div>
        </div>
        @endif



        <!-- Payment Methods -->
        @if($translator->paymentMethods && count($translator->paymentMethods) > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Payment Methods</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($translator->paymentMethods as $method)
                    <div class="col-md-6 mb-2">
                        <span class="badge bg-info">{{ ucfirst($method->type ?? 'N/A') }}</span>
                        @if($method->name)
                            <small class="text-muted"> - {{ $method->name }}</small>
                        @endif
                        @if($method->last_four)
                            <small class="text-muted"> •••• {{ $method->last_four }}</small>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Payout Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Payout Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>PayPal Email:</strong> {{ $translator->paypal_email ?? 'N/A' }}</p>
                        <p><strong>Bank Name:</strong> {{ $translator->bank_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($translator->bank_account_number)
                            <p><strong>Account Number:</strong> ****{{ substr($translator->bank_account_number, -4) }}</p>
                        @else
                            <p><strong>Account Number:</strong> N/A</p>
                        @endif
                        <p><strong>Routing Number:</strong> {{ $translator->bank_routing_number ?? 'N/A' }}</p>
                        <p><strong>Account Holder:</strong> {{ $translator->bank_account_holder_name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certifications -->
        @if($translator->certifications)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Certifications</h6>
            </div>
            <div class="card-body">
                @php
                    $certs = is_array($translator->certifications) ? $translator->certifications : json_decode($translator->certifications, true);
                @endphp
                @if($certs && count($certs) > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($certs as $cert)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ is_array($cert) ? ($cert['name'] ?? 'Certification') : $cert }}</span>
                                @if(is_array($cert) && isset($cert['issued_by']))
                                    <small class="text-muted">{{ $cert['issued_by'] }}</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No certifications listed</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Reviews -->
        @if($translator->reviews && count($translator->reviews) > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Recent Reviews ({{ $translator->reviews->count() }} total)</h6>
            </div>
            <div class="card-body">
                @foreach($translator->reviews->take(5) as $review)
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

        <!-- Portfolio/Work Samples -->
        @if($translator->portfolio_samples)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Portfolio/Work Samples</h6>
            </div>
            <div class="card-body">
                @php
                    $samples = is_array($translator->portfolio_samples) ? $translator->portfolio_samples : json_decode($translator->portfolio_samples, true);
                @endphp
                @if($samples && count($samples) > 0)
                    <div class="row">
                        @foreach($samples as $sample)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $sample['title'] ?? 'Sample Work' }}</h6>
                                    <p class="card-text">{{ $sample['description'] ?? 'No description' }}</p>
                                    @if(isset($sample['file_path']))
                                        <a href="{{ asset('storage/' . $sample['file_path']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i> View Sample
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No portfolio samples available</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Rejection Reason (if rejected) -->
        @if($translator->rejection_reason)
        <div class="card shadow mb-4 border-danger overflow-hidden">
            <div class="p-3 bg-danger text-white">
                <h6 class="m-0 font-weight-bold">Rejection Reason</h6>
            </div>
            <div class="card-body">
                <p class="text-danger">{{ $translator->rejection_reason }}</p>
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
                <h5 class="modal-title" id="rejectionModalLabel">Reject Translator Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectionForm">
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label">Reason for Rejection</label>
                        <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Please provide a reason for rejecting this translator's application..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRejection()">Reject Translator</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Update translator status (approve/reject)
    let currentTranslatorId = null;
    
    function updateStatus(translatorId, status) {
        if (status === 'reject') {
            // Show rejection modal
            currentTranslatorId = translatorId;
            const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
            modal.show();
            return;
        }
        
        if (!confirm('Are you sure you want to approve this translator?')) {
            return;
        }
        
        performStatusUpdate(translatorId, status);
    }
    
    function confirmRejection() {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) {
            alert('Please provide a reason for rejection.');
            return;
        }
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('rejectionModal'));
        modal.hide();
        performStatusUpdate(currentTranslatorId, 'reject', reason);
    }
    
    function performStatusUpdate(translatorId, status, rejectionReason = null) {
        const data = { 
            is_verified: status === 'approve',
        };
        if (rejectionReason) {
            data.rejection_reason = rejectionReason;
        }
        
        fetch(`/admin/translators/${translatorId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(async response => {
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error('Unexpected response format. You may need to log in again.');
            }
            const data = await response.json();
            if (!response.ok || !data.success) {
                const msg = data.message || 'Failed to update status';
                throw new Error(msg);
            }
            return data;
        })
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
    
    // Toggle availability
    function toggleAvailability(translatorId, available) {
        fetch(`/admin/translators/${translatorId}/toggle-availability`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ available: available })
        })
        .then(async response => {
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error('Unexpected response format. You may need to log in again.');
            }
            const data = await response.json();
            if (!response.ok || !data.success) {
                const msg = data.message || 'Failed to update availability';
                throw new Error(msg);
            }
            return data;
        })
        .then(data => {
            console.log('Availability updated successfully');
            setTimeout(() => { window.location.reload(); }, 800);
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'An error occurred while updating availability');
            // Reset the toggle to its previous state
            document.getElementById('availability').checked = !available;
        });
    }
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
@extends('layouts.admin')

@section('title', 'Doctor Management')
@section('subtitle', 'Manage and monitor doctor accounts and verifications')

@section('actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-primary" onclick="exportDoctors()">
        <i class="bi bi-download me-1"></i>
        Export
    </button>
    <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>
        Add Doctor
    </a>
</div>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Approved Doctors</h6>
                        <h3 class="stat-value text-success">{{ $stats['approved'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Pending Approval</h6>
                        <h3 class="stat-value text-warning">{{ $stats['pending'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-warning">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Rejected</h6>
                        <h3 class="stat-value text-danger">{{ $stats['rejected'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Video Enabled</h6>
                        <h3 class="stat-value text-info">{{ $stats['video_enabled'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-info">
                        <i class="bi bi-camera-video"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Component -->
@include('components.search-filter', [
    'action' => route('admin.doctors.index'),
    'searchLabel' => 'Search Doctors',
    'searchPlaceholder' => 'Search by name, email, or specialization...',
    'stats' => $stats,
    'showVideoConsultation' => true,
    'videoEnabledValue' => 'yes',
    'videoDisabledValue' => 'no'
])

<!-- Bulk Actions -->
@include('components.bulk-actions', [
    'labelPlural' => 'doctors',
    'bulkActionUrl' => route('admin.doctors.bulk-action')
])

<!-- Doctors Table -->
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-heart-pulse me-2"></i>
                Doctors List
            </h5>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll">
                <label class="form-check-label" for="selectAll">
                    Select All
                </label>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <small class="text-muted">
                Showing {{ $doctors->firstItem() ?? 0 }} to {{ $doctors->lastItem() ?? 0 }} of {{ $doctors->total() }} results
            </small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th width="50">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllTable">
                            </div>
                        </th>
                        <th>Doctor</th>
                        <th>Contact & Location</th>
                        <th>Specialization</th>
                        <th>Status</th>
                        <th>Video Consultation</th>
                        <th>Joined Date</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doctor)
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input bulk-checkbox" type="checkbox" value="{{ $doctor->id }}">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    @if($doctor->profile_image && file_exists(public_path('storage/' . $doctor->profile_image)))
                                        <img src="{{ asset('storage/' . $doctor->profile_image) }}" 
                                             alt="{{ $doctor->name }}" 
                                             class="rounded-circle" 
                                             style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white" 
                                             style="width: 45px; height: 45px; font-weight: 600;">
                                            {{ substr($doctor->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $doctor->name }}</h6>
                                    <small class="text-muted">ID: DR{{ $doctor->id }}</small>
                                    @if($doctor->professional_id)
                                        <br><small class="text-muted">{{ $doctor->professional_id }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-envelope me-2 text-muted"></i>
                                    <span>{{ $doctor->email }}</span>
                                </div>
                                @if($doctor->phone)
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-telephone me-2 text-muted"></i>
                                    <span>{{ $doctor->country_code }} {{ $doctor->phone }}</span>
                                </div>
                                @endif
                                @if($doctor->city)
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt me-2 text-muted"></i>
                                    <span>{{ $doctor->city }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($doctor->specialization)
                                <span class="badge bg-light text-dark">{{ $doctor->specialization }}</span>
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                            @if($doctor->years_of_experience)
                                <br><small class="text-muted">{{ $doctor->years_of_experience }} years exp.</small>
                            @endif
                        </td>
                        <td>
                            @if($doctor->is_verified === true)
                                <span class="badge bg-success">Approved</span>
                            @elseif($doctor->is_verified === false && $doctor->rejection_reason)
                                <span class="badge bg-danger" 
                                      title="{{ $doctor->rejection_reason }}">Rejected</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input video-toggle" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="video{{ $doctor->id }}" 
                                       data-doctor-id="{{ $doctor->id }}"
                                       {{ $doctor->can_video_consult ? 'checked' : '' }}
                                       {{ !$doctor->is_verified ? 'disabled' : '' }}>
                                <label class="form-check-label" for="video{{ $doctor->id }}">
                                    {{ $doctor->can_video_consult ? 'Enabled' : 'Disabled' }}
                                </label>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>{{ $doctor->created_at->format('M d, Y') }}</span>
                                <br>
                                <small class="text-muted">{{ $doctor->created_at->diffForHumans() }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.doctors.show', $doctor->id) }}" 
                                   class="btn btn-outline-info btn-sm"
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.doctors.edit', $doctor->id) }}" 
                                   class="btn-edit btn-sm"
                                   title="Edit Doctor">
                                    <i class="bi bi-pencil"></i>
                                    <span>Edit</span>
                                </a>
                                
                                <!-- Status Actions -->
                                @if(!$doctor->is_verified || $doctor->is_verified === null)
                                    <button type="button" 
                                            class="btn btn-success btn-sm" 
                                            onclick="updateDoctorStatus({{ $doctor->id }}, 'approve')"
                                            title="Approve Doctor">
                                        <i class="bi bi-check"></i>
                                    </button>
                                @endif
                                
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm" 
                                        onclick="deleteDoctor({{ $doctor->id }}, '{{ $doctor->name }}')"
                                        title="Delete Doctor">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div>
                                <i class="bi bi-heart-pulse text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">No doctors found</h5>
                                <p class="text-muted">Try adjusting your search criteria or add a new doctor.</p>
                                <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
                                    <i class="bi bi-person-plus me-2"></i>
                                    Add First Doctor
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($doctors->hasPages())
    <div class="card-footer">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <small class="text-muted">
                    Showing {{ $doctors->firstItem() }} to {{ $doctors->lastItem() }} of {{ $doctors->total() }} results
                </small>
            </div>
            <div>
                {{ $doctors->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bi bi-x-circle text-warning me-2"></i>
                    Reject Doctor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You are about to reject doctor <strong id="doctorName"></strong>.</p>
                <div class="mb-3">
                    <label for="rejectionReason" class="form-label">Reason for rejection <span class="text-danger">*</span></label>
                    <textarea class="form-control" 
                              id="rejectionReason" 
                              rows="3" 
                              placeholder="Please provide a detailed reason for rejection..."
                              required></textarea>
                    <div class="form-text">This reason will be visible to the doctor.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmReject">
                    <i class="bi bi-x me-2"></i>
                    Reject Doctor
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete doctor <strong id="deleteDoctorName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone. All doctor data, including patient consultations and records, will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer gap-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash me-2"></i>
                    Delete Doctor
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentDoctorId = null;

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
        setupFilterListeners();
    });

    function initializeEventListeners() {
        // Video consultation toggle listeners
        document.querySelectorAll('.video-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const doctorId = this.getAttribute('data-doctor-id');
                const enabled = this.checked;
                toggleVideoConsultation(doctorId, enabled, this);
            });
        });

        // Modal event listeners
        const confirmRejectBtn = document.getElementById('confirmReject');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        
        if (confirmRejectBtn) {
            confirmRejectBtn.addEventListener('click', function() {
                const reason = document.getElementById('rejectionReason').value.trim();
                if (!reason) {
                    alert('Please provide a reason for rejection');
                    return;
                }
                if (currentDoctorId) {
                    updateDoctorStatus(currentDoctorId, 'reject', reason);
                }
            });
        }
        
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (currentDoctorId) {
                    performDelete(currentDoctorId);
                }
            });
        }
    }

    // Update doctor status (approve/reject)
    function updateDoctorStatus(doctorId, action, reason = null) {
        const data = { status: action };
        if (reason) {
            data.rejection_reason = reason;
        }

        fetch(`/admin/doctors/${doctorId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                // Close modal if open
                const rejectModal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                if (rejectModal) {
                    rejectModal.hide();
                }
                // Reload page after successful action
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while updating doctor status');
        });
    }

    // Toggle video consultation
    function toggleVideoConsultation(doctorId, enabled, toggleElement) {
        const originalDisabled = toggleElement.disabled;
        toggleElement.disabled = true;

        fetch(`/admin/doctors/${doctorId}/toggle-video`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ enabled: enabled })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update label
                const label = toggleElement.parentElement.querySelector('label');
                if (label) {
                    label.textContent = enabled ? 'Enabled' : 'Disabled';
                }
                
                // Update status cards
                updateStatusCards();
                
                showAlert('success', 'Video consultation setting updated successfully');
            } else {
                // Revert toggle state
                toggleElement.checked = !enabled;
                showAlert('error', data.message || 'Failed to update video consultation setting');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert toggle state
            toggleElement.checked = !enabled;
            showAlert('error', 'An error occurred while updating video consultation setting');
        })
        .finally(() => {
            toggleElement.disabled = originalDisabled;
        });
    }

    function updateStatusCards() {
        fetch(`/admin/doctors?ajax=1`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.stats) {
                // Update video enabled count
                const videoEnabledCard = document.querySelector('.stat-value.text-info');
                if (videoEnabledCard) {
                    videoEnabledCard.textContent = data.stats.video_enabled;
                }
                
                // Update filter dropdown counts
                const videoConsultSelect = document.getElementById('video_consult');
                if (videoConsultSelect) {
                    const allOption = videoConsultSelect.querySelector('option[value=""]');
                    const enabledOption = videoConsultSelect.querySelector('option[value="yes"]');
                    const disabledOption = videoConsultSelect.querySelector('option[value="no"]');
                    
                    if (allOption) allOption.textContent = `All (${data.stats.total})`;
                    if (enabledOption) enabledOption.textContent = `Enabled (${data.stats.video_enabled})`;
                    if (disabledOption) disabledOption.textContent = `Disabled (${data.stats.total - data.stats.video_enabled})`;
                }
            }
        })
        .catch(error => {
            console.error('Error updating status cards:', error);
        });
    }

    // Show reject modal
    function showRejectModal(doctorId, doctorName) {
        currentDoctorId = doctorId;
        document.getElementById('doctorName').textContent = doctorName;
        document.getElementById('rejectionReason').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }

    // Delete doctor
    function deleteDoctor(doctorId, doctorName) {
        currentDoctorId = doctorId;
        document.getElementById('deleteDoctorName').textContent = doctorName;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    function performDelete(doctorId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/doctors/${doctorId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }

    // Export doctors
    function exportDoctors() {
        const btn = event.target.closest('button');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';
        btn.disabled = true;
        
        // Redirect to export route
        window.location.href = '{{ route("admin.doctors.export") }}';
        
        // Reset button after a short delay
        setTimeout(() => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }, 2000);
    }

    // Show alert function
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' || type === 'danger' ? 'exclamation-triangle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 4 seconds
        setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 4000);
    }

    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
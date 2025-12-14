@extends('layouts.admin')

@section('title', 'Laboratory Management')
@section('subtitle', 'Manage and monitor laboratory accounts and verifications')

@section('actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-primary" onclick="exportLaboratories()">
        <i class="bi bi-download me-1"></i>
        Export
    </button>
    <a href="{{ route('admin.laboratories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus me-1"></i>
        Add Laboratory
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
                        <h6 class="stat-label">Approved Laboratories</h6>
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
    'action' => route('admin.laboratories.index'),
    'searchLabel' => 'Search Laboratories',
    'searchPlaceholder' => 'Search by name, email, or phone...',
    'stats' => $stats,
    'showVideoConsultation' => true,
    'videoEnabledValue' => '1',
    'videoDisabledValue' => '0'
])

@include('components.bulk-actions', [
    'labelPlural' => 'laboratories',
    'bulkActionUrl' => route('admin.laboratories.bulk-action')
])

<!-- Laboratories Table -->
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-flask me-2"></i>
                Laboratories List
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
                Showing {{ $laboratories->firstItem() ?? 0 }} to {{ $laboratories->lastItem() ?? 0 }} of {{ $laboratories->total() }} results
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
                        <th>Laboratory</th>
                        <th>Contact & Location</th>
                        <th>Services</th>
                        <th>Status</th>
                        <th>Video Consultation</th>
                        <th>Joined Date</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laboratories as $laboratory)
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input bulk-checkbox" type="checkbox" value="{{ $laboratory->id }}">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    @if($laboratory->profile_image && file_exists(public_path('storage/' . $laboratory->profile_image)))
                                    <img src="{{ asset('storage/' . $laboratory->profile_image) }}" 
                                             alt="{{ $laboratory->name }}" 
                                             class="rounded-circle" 
                                             style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                             style="width: 45px; height: 45px; font-weight: 600;">
                                            {{ substr($laboratory->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $laboratory->name }}</h6>
                                    <small class="text-muted">ID: LAB{{ $laboratory->id }}</small>
                                    @if($laboratory->license_number)
                                        <br><small class="text-muted">{{ $laboratory->license_number }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-envelope me-2 text-muted"></i>
                                    <span>{{ $laboratory->user->email }}</span>
                                </div>
                                @if($laboratory->phone)
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-telephone me-2 text-muted"></i>
                                    <span>{{ $laboratory->phone }}</span>
                                </div>
                                @endif
                                @if($laboratory->city)
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt me-2 text-muted"></i>
                                    <span>{{ $laboratory->city }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($laboratory->services && $laboratory->services->count() > 0)
                                @foreach($laboratory->services->take(2) as $service)
                                    <span class="badge bg-light text-dark me-1 mb-1">{{ $service->name }}</span>
                                @endforeach
                                @if($laboratory->services->count() > 2)
                                    <br><small class="text-muted">+{{ $laboratory->services->count() - 2 }} more</small>
                                @endif
                            @else
                                <span class="text-muted">No services</span>
                            @endif
                        </td>
                        <td>
                            @if($laboratory->is_verified && is_null($laboratory->rejection_reason))
                                <span class="badge bg-success">Approved</span>
                            @elseif(!$laboratory->is_verified && !is_null($laboratory->rejection_reason))
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input video-toggle" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="video{{ $laboratory->id }}" 
                                       data-laboratory-id="{{ $laboratory->id }}"
                                       {{ $laboratory->can_video_consult ? 'checked' : '' }}
                                       {{ !$laboratory->is_verified || !is_null($laboratory->rejection_reason) ? 'disabled' : '' }}>
                                <label class="form-check-label" for="video{{ $laboratory->id }}">
                                    {{ $laboratory->can_video_consult ? 'Enabled' : 'Disabled' }}
                                </label>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>{{ $laboratory->user->created_at->format('M d, Y') }}</span>
                                <br>
                                <small class="text-muted">{{ $laboratory->user->created_at->diffForHumans() }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.laboratories.show', $laboratory->id) }}" 
                                   class="btn btn-outline-info btn-sm"
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.laboratories.edit', $laboratory->id) }}" 
                                   class="btn-edit btn-sm"
                                   title="Edit Laboratory">
                                    <i class="bi bi-pencil"></i>
                                    <span>Edit</span>
                                </a>
                                
                                <!-- Status Actions -->
                                @if(!$laboratory->is_verified && is_null($laboratory->rejection_reason))
                                    <button type="button" 
                                            class="btn btn-success btn-sm" 
                                            onclick="updateLaboratoryStatus({{ $laboratory->id }}, 'approve')"
                                            title="Approve Laboratory">
                                        <i class="bi bi-check"></i>
                                    </button>
                                @endif
                                
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm" 
                                        onclick="deleteLaboratory({{ $laboratory->id }}, '{{ $laboratory->name }}')"
                                        title="Delete Laboratory">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div>
                                <i class="bi bi-flask text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">No laboratories found</h5>
                                <p class="text-muted">Try adjusting your search criteria or add a new laboratory.</p>
                                <a href="{{ route('admin.laboratories.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus me-2"></i>
                                    Add First Laboratory
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($laboratories->hasPages())
    <div class="card-footer">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <small class="text-muted">
                    Showing {{ $laboratories->firstItem() }} to {{ $laboratories->lastItem() }} of {{ $laboratories->total() }} results
                </small>
            </div>
            <div>
                {{ $laboratories->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Reject Laboratory
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject <strong id="laboratoryName"></strong>?</p>
                <div class="mb-3">
                    <label for="rejectionReason" class="form-label">Reason for rejection <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejectionReason" rows="3" 
                              placeholder="Please provide a reason for rejecting this laboratory..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmReject">
                    <i class="bi bi-x-circle me-2"></i>
                    Reject Laboratory
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog w-50">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-trash text-danger me-2"></i>
                    Delete Laboratory
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to permanently delete <strong id="deleteLaboratoryName"></strong>?</p>
                <p class="text-muted small">This will also delete:</p>
                <ul class="text-muted small">
                    <li>Laboratory profile and all associated data</li>
                    <li>User account and login credentials</li>
                    <li>All reviews and ratings</li>
                    <li>Gallery images and documents</li>
                </ul>
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash me-2"></i>
                    Delete Laboratory
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentLaboratoryId = null;

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
        setupFilterListeners();
        initializeTooltips();
    });

    function initializeEventListeners() {
        // Video consultation toggle listeners
        document.querySelectorAll('.video-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const laboratoryId = this.getAttribute('data-laboratory-id');
                const enabled = this.checked;
                toggleVideoConsultation(laboratoryId, enabled, this);
            });
        });

        // Modal event listeners
        const confirmRejectBtn = document.getElementById('confirmReject');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        
        if (confirmRejectBtn) {
            confirmRejectBtn.addEventListener('click', function() {
                const reason = document.getElementById('rejectionReason').value.trim();
                if (!reason) {
                    showAlert('error', 'Please provide a reason for rejection');
                    return;
                }
                if (currentLaboratoryId) {
                    updateLaboratoryStatus(currentLaboratoryId, 'reject', reason);
                }
            });
        }
        
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (currentLaboratoryId) {
                    performDelete(currentLaboratoryId);
                }
            });
        }
    }

    function setupFilterListeners() {
        // Auto-submit form when filters change (except search)
        const statusSelect = document.getElementById('status');
        const videoConsultSelect = document.getElementById('video_consult');
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        
        [statusSelect, videoConsultSelect, dateFromInput, dateToInput].forEach(element => {
            if (element) {
                element.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
        });

        // Search with debounce
        const searchInput = document.getElementById('search');
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    if (this.value.length >= 3 || this.value.length === 0) {
                        document.getElementById('filterForm').submit();
                    }
                }, 500);
            });
        }
    }

    // Update laboratory status (approve/reject)
    function updateLaboratoryStatus(laboratoryId, action, reason = null) {
        const isVerified = action === 'approve';
        const data = { is_verified: isVerified };
        if (!isVerified && reason) {
            data.rejection_reason = reason;
        }

        fetch(`/admin/laboratories/${laboratoryId}/update-status`, {
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
                const msg = data.message || 'Laboratory status updated successfully';
                if (typeof showAlert === 'function') {
                    showAlert('success', msg);
                }
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
                const msg = data.message || 'Failed to update laboratory status';
                if (typeof showAlert === 'function') {
                    showAlert('error', msg);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof showAlert === 'function') {
                showAlert('error', 'An error occurred while updating laboratory status');
            }
        });
    }

    // Toggle video consultation
    function toggleVideoConsultation(laboratoryId, enabled, toggleElement) {
        const originalDisabled = toggleElement.disabled;
        toggleElement.disabled = true;

        fetch(`/admin/laboratories/${laboratoryId}/toggle-video`, {
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
                
                showAlert('success', data.message || 'Video consultation setting updated successfully');
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

    // Update status cards after video consultation toggle
    function updateStatusCards() {
        fetch(`/admin/laboratories?ajax=1`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.stats) {
                // Update video enabled count in status cards
                const videoEnabledCard = document.querySelector('.stat-value.text-info');
                if (videoEnabledCard) {
                    videoEnabledCard.textContent = data.stats.video_enabled;
                }
                
                // Update filter dropdown counts
                updateFilterCounts(data.stats);
            }
        })
        .catch(error => {
            console.error('Error updating status cards:', error);
        });
    }

    // Update filter dropdown counts
    function updateFilterCounts(stats) {
        const videoConsultSelect = document.getElementById('video_consult');
        if (videoConsultSelect) {
            const options = videoConsultSelect.querySelectorAll('option');
            if (options.length >= 3) {
                // Update "All" option
                options[0].textContent = `All (${stats.total})`;
                // Update "Enabled" option
                options[1].textContent = `Enabled (${stats.video_enabled})`;
                // Update "Disabled" option
                options[2].textContent = `Disabled (${stats.total - stats.video_enabled})`;
            }
        }
        
        // Also update status filter counts
        const statusSelect = document.getElementById('status');
        if (statusSelect) {
            const statusOptions = statusSelect.querySelectorAll('option');
            if (statusOptions.length >= 4) {
                // Update status filter counts
                statusOptions[0].textContent = `All Status (${stats.total})`;
                statusOptions[1].textContent = `Approved (${stats.approved})`;
                statusOptions[2].textContent = `Pending (${stats.pending})`;
                statusOptions[3].textContent = `Rejected (${stats.rejected})`;
            }
        }
    }

    // Show reject modal
    function showRejectModal(laboratoryId, laboratoryName) {
        currentLaboratoryId = laboratoryId;
        document.getElementById('laboratoryName').textContent = laboratoryName;
        document.getElementById('rejectionReason').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }

    // Delete laboratory
    function deleteLaboratory(laboratoryId, laboratoryName) {
        currentLaboratoryId = laboratoryId;
        document.getElementById('deleteLaboratoryName').textContent = laboratoryName;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    function performDelete(laboratoryId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/laboratories/${laboratoryId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }

    // Export laboratories
    function exportLaboratories() {
        const btn = event.target.closest('button');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';
        btn.disabled = true;
        
        // Create a temporary link to trigger the download
        const link = document.createElement('a');
        link.href = '{{ route("admin.laboratories.export") }}';
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Reset button after a short delay
        setTimeout(() => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
            showAlert('success', 'Export completed successfully!');
        }, 1500);
    }

    // Enhanced alert function with better styling
    function showAlert(type, message) {
        // Remove existing alerts
        document.querySelectorAll('.alert-dismissible').forEach(alert => alert.remove());
        
        const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
        const iconClass = {
            'success': 'bi-check-circle',
            'error': 'bi-exclamation-triangle',
            'warning': 'bi-exclamation-triangle',
            'info': 'bi-info-circle'
        }[type] || 'bi-info-circle';
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                const alert = bootstrap.Alert.getOrCreateInstance(alertDiv);
                alert.close();
            }
        }, 5000);
    }

    // Initialize tooltips
    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
</script>
@endpush
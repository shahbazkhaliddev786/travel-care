@extends('layouts.admin')

@section('title', 'Translator Management')
@section('subtitle', 'Manage and monitor translator accounts')
@section('actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="window.exportTranslators ? window.exportTranslators() : null">
            <i class="bi bi-download me-1"></i>
            Export
        </button>
        <a href="{{ route('admin.translators.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            Add Translator
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">


    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="stat-label">Approved Translators</h6>
                            <h3 class="stat-value text-success">{{ $stats['approved'] ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon text-success">
                            <i class="bi bi-check-circle"></i>
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
                            <h6 class="stat-label">Available Now</h6>
                            <h3 class="stat-value text-info">{{ $stats['available'] ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon text-info">
                            <i class="bi bi-person-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>
                Search & Filter
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.translators.index') }}" class="row g-3" id="filterForm">
                <div class="col-lg-4 col-md-6">
                    <label for="search" class="form-label">Search Translators</label>
                    <div class="enhanced-search">
                        <input type="text" 
                               class="form-control" 
                               id="search"
                               name="search" 
                               placeholder="Search by name, email, or phone..." 
                               value="{{ request('search') }}">
                        <i class="bi bi-search search-icon"></i>
                    </div>
                </div>
                
                <div class="  col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status">
                        <option value="">All Status</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                
                <div class="  col-md-3">
                    <label for="availability" class="form-label">Availability</label>
                    <select class="form-select" id="availability" name="availability">
                        <option value="">All</option>
                        <option value="1" {{ request('availability') == '1' ? 'selected' : '' }}>Available</option>
                        <option value="0" {{ request('availability') == '0' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
                
                <div class="  col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" 
                           class="form-control" 
                           name="date_from" 
                           id="date_from"
                           value="{{ request('date_from') }}">
                </div>
                
                <div class="  col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" 
                           class="form-control" 
                           name="date_to" 
                           id="date_to"
                           value="{{ request('date_to') }}">
                </div>
                
                <div class="  col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.translators.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    @include('components.bulk-actions', [
        'labelPlural' => 'translators',
        'bulkActionUrl' => route('admin.translators.bulk-action'),
        'showApprovalButtons' => true
    ])

    <!-- Translators Table -->
    <div class="card">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bi bi-translate me-2"></i>
                Translators List
            </h5>
            <div class="d-flex align-items-center gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        Select All
                    </label>
                </div>
                <small class="text-muted">
                    Showing {{ $translators->firstItem() ?? 0 }} to {{ $translators->lastItem() ?? 0 }} of {{ $translators->total() }} results
                </small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAllTable" class="form-check-input">
                            </th>
                            <th>Translator</th>
                            <th>Contact & Location</th>
                            <th>Languages & Experience</th>
                            <th>Status</th>
                            <th>Availability</th>
                            <th>Joined</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($translators as $translator)
                        <tr data-translator-id="{{ $translator->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input bulk-checkbox" value="{{ $translator->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        @php $photo = $translator->profile_image ?? $translator->profile_photo; @endphp
                                        @if($photo && file_exists(public_path('storage/' . $photo)))
                                            <img src="{{ asset('storage/' . $photo) }}" 
                                                     alt="{{ $translator->name }}" 
                                                     class="rounded-circle" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                                     style="width: 40px; height: 40px; font-weight: 600;">
                                                    {{ substr($translator->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $translator->name }}</h6>
                                        <small class="text-muted">ID: #{{ $translator->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-envelope me-2 text-muted"></i>
                                        <span>{{ $translator->user->email }}</span>
                                    </div>
                                    @if($translator->phone)
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-telephone me-2 text-muted"></i>
                                        <span>{{ $translator->phone }}</span>
                                    </div>
                                    @endif
                                    @if($translator->city || $translator->country)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt me-2 text-muted"></i>
                                        <span>{{ $translator->city }}@if($translator->country), {{ $translator->country }}@endif</span>
                                    </div>
                                    @endif
                                    @if(!$translator->phone && !$translator->city)
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    @php
                                        $languages = $translator->languages ? (is_array($translator->languages) ? $translator->languages : json_decode($translator->languages, true)) : [];
                                    @endphp
                                    @if($languages && count($languages) > 0)
                                        @foreach(array_slice($languages, 0, 2) as $language)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $language }}</span>
                                        @endforeach
                                        @if(count($languages) > 2)
                                            <span class="badge bg-light text-dark">+{{ count($languages) - 2 }} more</span>
                                        @endif
                                    @else
                                        <span class="text-muted">No languages</span>
                                    @endif
                                    @if($translator->years_of_experience)
                                        <div class="mt-1">
                                            <i class="bi bi-calendar me-1"></i>{{ $translator->years_of_experience }} years exp.
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($translator->is_verified === true)
                                    <span class="badge bg-success">Approved</span>
                                @elseif($translator->is_verified === false && !is_null($translator->rejection_reason))
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.translators.toggle-availability', $translator->id) }}">
                                    @csrf
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="available" value="{{ $translator->is_available ? 1 : 0 }}">
                                        <input class="form-check-input availability-toggle"
                                               type="checkbox"
                                               {{ $translator->is_available ? 'checked' : '' }}
                                               data-translator-id="{{ $translator->id }}"
                                               onchange="this.form.available.value = this.checked ? 1 : 0; this.form.submit();"
                                               {{ !$translator->is_verified ? 'disabled' : '' }}>
                                        <label class="form-check-label small">
                                            {{ $translator->is_available ? 'Available' : 'Unavailable' }}
                                        </label>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <div>
                                    <span>{{ $translator->user->created_at->format('M d, Y') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $translator->user->created_at->diffForHumans() }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.translators.show', $translator->id) }}" 
                                       class="btn btn-outline-info btn-sm" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.translators.edit', $translator->id) }}" 
                                       class="btn-edit btn-sm" title="Edit Translator">
                                        <i class="bi bi-pencil"></i>
                                        <span>Edit</span>
                                    </a>
                                    @if($translator->status == 'pending')
                                        <button type="button" 
                                                class="btn btn-outline-success btn-sm" 
                                                onclick="updateTranslatorStatus({{ $translator->id }}, 'approve')"
                                                title="Approve">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-warning btn-sm" 
                                                onclick="showRejectModal({{ $translator->id }}, '{{ $translator->name }}')"
                                                title="Reject">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    @endif
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteTranslator({{ $translator->id }}, '{{ $translator->name }}')"
                                            title="Delete Translator">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div>
                                    <i class="bi bi-translate text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3 text-muted">No translators found</h5>
                                    <p class="text-muted">Try adjusting your search criteria or add a new translator.</p>
                                    <a href="{{ route('admin.translators.create') }}" class="btn btn-primary">
                                        <i class="bi bi-person-plus me-2"></i>
                                        Add First Translator
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($translators->hasPages())
        <div class="card-footer">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div>
                    <small class="text-muted">
                        Showing {{ $translators->firstItem() }} to {{ $translators->lastItem() }} of {{ $translators->total() }} results
                    </small>
                </div>
                <div>
                    {{ $translators->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
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
                    <p>Are you sure you want to delete translator <strong id="deleteTranslatorName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        This action cannot be undone. All translator data will be permanently deleted.
                    </div>
                </div>
                <div class="modal-footer gap-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">
                        <i class="bi bi-trash me-2"></i>
                        Delete Translator
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        var currentTranslatorId = null;
    
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeAvailabilityListeners();
            // Selection state handled by bulk-actions component
            setupFilterListeners();
        });
    
        function initializeAvailabilityListeners() {
            // Availability toggles are submitted via inline form for reliability across environments.
            
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
                    if (currentTranslatorId) {
                        updateTranslatorStatus(currentTranslatorId, 'reject', reason);
                    }
                });
            }
            
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function() {
                    if (currentTranslatorId) {
                        performDelete(currentTranslatorId);
                    }
                });
            }
        }
    
        function setupFilterListeners() {
            // Auto-submit form when filters change (except search)
            const statusSelect = document.getElementById('status');
            const availabilitySelect = document.getElementById('availability');
            const dateFromInput = document.getElementById('date_from');
            
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
            
            if (availabilitySelect) {
                availabilitySelect.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
            
            if (dateFromInput) {
                dateFromInput.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
    
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

        // Update translator status (approve/reject)
        function updateTranslatorStatus(translatorId, action, reason = null) {
            const isVerified = action === 'approve';
            const data = { is_verified: isVerified };
            if (!isVerified) {
                data.rejection_reason = (reason && reason.trim()) ? reason.trim() : 'Rejected by admin';
            }
    
            fetch(`/admin/translators/${translatorId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message || (isVerified ? 'Translator approved successfully' : 'Translator rejected successfully'));
                    // Close modal if open
                    const rejectModalEl = document.getElementById('rejectModal');
                    if (rejectModalEl) {
                        const rejectModal = bootstrap.Modal.getInstance(rejectModalEl);
                        if (rejectModal) rejectModal.hide();
                    }
                    // Reload page to reflect status badge and cards
                    setTimeout(() => { location.reload(); }, 1200);
                } else {
                    showAlert('error', data.message || 'Failed to update translator status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while updating translator status');
            });
        }

        // Toggle availability
        function toggleAvailability(translatorId, available, toggleElement) {
            const originalDisabled = toggleElement.disabled;
            toggleElement.disabled = true;
    
            fetch(`/admin/translators/${translatorId}/toggle-availability`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ available: available })
            })
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    // Likely received an HTML response (e.g., login redirect or error page)
                    const text = await response.text();
                    throw new Error('Unexpected response format. You may need to log in again.');
                }
                const data = await response.json();
                if (!response.ok || !data.success) {
                    const msg = data.message || 'Failed to update availability setting';
                    throw new Error(msg);
                }
                return data;
            })
            .then(data => {
                // Update label
                const label = toggleElement.parentElement.querySelector('label');
                if (label) {
                    label.textContent = available ? 'Available' : 'Unavailable';
                }
                showAlert('success', 'Availability setting updated successfully');
                // Reload to refresh status cards and filters
                setTimeout(() => { location.reload(); }, 1000);
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert toggle state
                toggleElement.checked = !available;
                showAlert('error', error.message || 'An error occurred while updating availability setting');
            })
            .finally(() => {
                toggleElement.disabled = originalDisabled;
            });
        }

        // Show reject modal
        function showRejectModal(translatorId, translatorName) {
            currentTranslatorId = translatorId;
            document.getElementById('translatorName').textContent = translatorName;
            document.getElementById('rejectionReason').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        // Delete translator
        function deleteTranslator(translatorId, translatorName) {
            currentTranslatorId = translatorId;
            document.getElementById('deleteTranslatorName').textContent = translatorName;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        function performDelete(translatorId) {
            // Perform AJAX delete to remove row without full page reload
            fetch(`/admin/translators/${translatorId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                let data = null;
                if (contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    // If not JSON, treat non-OK as error
                    if (!response.ok) {
                        throw new Error('Unexpected response. Please try again.');
                    }
                }
                if (!response.ok || (data && data.success === false)) {
                    const msg = (data && data.message) ? data.message : 'Failed to delete translator';
                    throw new Error(msg);
                }

                // Hide modal if open
                const deleteModalEl = document.getElementById('deleteModal');
                if (deleteModalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(deleteModalEl) || new bootstrap.Modal(deleteModalEl);
                    modalInstance.hide();
                }

                // Remove row from table
                const row = document.querySelector(`tr[data-translator-id="${translatorId}"]`);
                if (row) {
                    row.remove();
                }

                // Show success alert
                showAlert('success', 'Translator deleted successfully');
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', error.message || 'An error occurred while deleting translator');
            });
        }

        // Export translators
        function exportTranslators() {
            window.location.href = '{{ route("admin.translators.export") }}';
        }

        // Ensure global access from inline onclick
        window.exportTranslators = exportTranslators;

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
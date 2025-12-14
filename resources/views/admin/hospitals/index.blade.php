@extends('layouts.admin')

@section('title', 'Hospital Management')
@section('subtitle', 'Manage and monitor hospital groups and facilities')

@section('actions')
<div class="d-flex gap-2">
    <button class="btn btn-outline-primary" onclick="exportHospitals()">
        <i class="bi bi-download me-1"></i>
        Export
    </button>
    <a href="{{ route('admin.hospitals.create') }}" class="btn btn-primary">
        <i class="bi bi-building-add me-1"></i>
        Add Hospital
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
                        <h6 class="stat-label">Approved Hospitals</h6>
                        <h3 class="stat-value text-success">{{ $stats['approved'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="bi bi-building-check"></i>
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
                        <i class="bi bi-building-x"></i>
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
                        <h6 class="stat-label">Total Hospitals</h6>
                        <h3 class="stat-value text-primary">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-primary">
                        <i class="bi bi-buildings"></i>
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
        <form method="GET" action="{{ route('admin.hospitals.index') }}" class="row g-3" id="filterForm">
            <div class="col-lg-4 col-md-6">
                <label for="search" class="form-label">Search Hospitals</label>
                <div class="enhanced-search">
                    <input type="text" 
                           class="form-control" 
                           id="search"
                           name="search" 
                           placeholder="Search by name, email, or city..." 
                           value="{{ request('search') }}">
                    <i class="bi bi-search search-icon"></i>
                </div>
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status">
                    <option value="">All Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" 
                       class="form-control" 
                       name="date_from" 
                       id="date_from"
                       value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-3">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" 
                       class="form-control" 
                       name="date_to" 
                       id="date_to"
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i>
                        Filter
                    </button>
                    <a href="{{ route('admin.hospitals.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Hospitals Table -->
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <h5 class="card-title mb-0">
            <i class="bi bi-buildings me-2"></i>
            Hospitals List
        </h5>
        <div class="d-flex align-items-center gap-3">
            <small class="text-muted">
                Showing {{ $hospitals->firstItem() ?? 0 }} to {{ $hospitals->lastItem() ?? 0 }} of {{ $hospitals->total() }} results
            </small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Hospital</th>
                        <th>Contact & Location</th>
                        <th>Status</th>
                        <th>Registered Date</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hospitals as $hospital)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    @if($hospital->logo)
                                        <img src="{{ asset('storage/' . $hospital->logo) }}" 
                                             alt="{{ $hospital->name }}" 
                                             class="rounded-circle" 
                                             style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                             style="width: 45px; height: 45px; font-weight: 600;">
                                            {{ substr($hospital->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $hospital->name }}</h6>
                                    <small class="text-muted">ID: H{{ $hospital->id }}</small>
                                    @if($hospital->professional_id)
                                        <br><small class="text-muted">{{ $hospital->professional_id }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-envelope me-2 text-muted"></i>
                                    <span>{{ $hospital->email }}</span>
                                </div>
                                @if($hospital->phone)
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-telephone me-2 text-muted"></i>
                                    <span>{{ $hospital->country_code }} {{ $hospital->phone }}</span>
                                </div>
                                @endif
                                @if($hospital->city)
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-geo-alt me-2 text-muted"></i>
                                    <span>{{ $hospital->city }}</span>
                                </div>
                                @endif
                                @if($hospital->address)
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-building me-2 text-muted"></i>
                                    <span class="text-truncate" style="max-width: 200px;">{{ $hospital->address }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($hospital->is_verified === 1)
                                <span class="badge bg-success">Approved</span>
                            @elseif($hospital->is_verified === -1)
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                <span>{{ $hospital->created_at->format('M d, Y') }}</span>
                                <br>
                                <small class="text-muted">{{ $hospital->created_at->diffForHumans() }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.hospitals.show', $hospital->id) }}" 
                                   class="btn btn-outline-info btn-sm"
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.hospitals.edit', $hospital->id) }}" 
                                   class="btn-edit btn-sm"
                                   title="Edit Hospital">
                                    <i class="bi bi-pencil"></i>
                                    Edit
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm" 
                                        onclick="deleteHospital({{ $hospital->id }}, '{{ $hospital->name }}')"
                                        title="Delete Hospital">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div>
                                <i class="bi bi-buildings text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">No hospitals found</h5>
                                <p class="text-muted">Try adjusting your search criteria or add a new hospital.</p>
                                <a href="{{ route('admin.hospitals.create') }}" class="btn btn-primary">
                                    <i class="bi bi-building-add me-2"></i>
                                    Add First Hospital
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($hospitals->hasPages())
    <div class="card-footer">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <small class="text-muted">
                    Showing {{ $hospitals->firstItem() }} to {{ $hospitals->lastItem() }} of {{ $hospitals->total() }} results
                </small>
            </div>
            <div>
                {{ $hospitals->appends(request()->query())->links() }}
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
                <p>Are you sure you want to delete hospital <strong id="deleteHospitalName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone. All hospital data, including gallery images and services, will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash me-2"></i>
                    Delete Hospital
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentHospitalId = null;

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
        setupFilterListeners();
    });

    function initializeEventListeners() {
        // Delete confirmation listener
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (currentHospitalId) {
                    performDelete(currentHospitalId);
                }
            });
        }
    }

    function setupFilterListeners() {
        // Auto-submit form when filters change (except search)
        const statusSelect = document.getElementById('status');
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        }
        
        if (dateFromInput) {
            dateFromInput.addEventListener('change', function() {
                // Auto-submit if both dates are filled or if only from date is filled
                const dateTo = document.getElementById('date_to').value;
                if (dateTo || this.value) {
                    document.getElementById('filterForm').submit();
                }
            });
        }
        
        if (dateToInput) {
            dateToInput.addEventListener('change', function() {
                // Auto-submit if both dates are filled or if only to date is filled
                const dateFrom = document.getElementById('date_from').value;
                if (dateFrom || this.value) {
                    document.getElementById('filterForm').submit();
                }
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

    // Delete hospital
    function deleteHospital(hospitalId, hospitalName) {
        currentHospitalId = hospitalId;
        document.getElementById('deleteHospitalName').textContent = hospitalName;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    function performDelete(hospitalId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/hospitals/${hospitalId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }

    // Export hospitals
    function exportHospitals() {
        const btn = event.target.closest('button');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';
        btn.disabled = true;
        
        // Redirect to export route
        window.location.href = '{{ route("admin.hospitals.export") }}';
        
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
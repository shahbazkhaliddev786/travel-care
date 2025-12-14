@extends('layouts.admin')

@section('title', 'Customer Management')
@section('subtitle', 'Manage and monitor customer accounts')

@section('actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="exportCustomers()">
            <i class="bi bi-download me-1"></i>
            Export
        </button>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>
            Add Customer
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
                        <h6 class="stat-label">Active Customers</h6>
                        <h3 class="stat-value text-success">{{ $stats['active'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="bi bi-person-check"></i>
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
                        <h6 class="stat-label">Inactive Customers</h6>
                        <h3 class="stat-value text-warning">{{ $stats['inactive'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-warning">
                        <i class="bi bi-person-x"></i>
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
                        <h6 class="stat-label">Total Customers</h6>
                        <h3 class="stat-value text-primary">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-primary">
                        <i class="bi bi-people"></i>
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
                        <h6 class="stat-label">New This Month</h6>
                        <h3 class="stat-value text-info">{{ $stats['new'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-info">
                        <i class="bi bi-person-plus"></i>
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
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3" id="filterForm">
            <div class="col-lg-4 col-md-6">
                <label for="search" class="form-label">Search Customers</label>
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
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Actions -->
<div class="card mb-4" id="bulkActionsCard" style="display: none;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center bulk-actions">
            <div>
                <strong id="selectedCount">0</strong> customers selected
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm" onclick="bulkAction('activate')">
                    <i class="bi bi-person-check me-1"></i>
                    Activate
                </button>
                <button class="btn btn-warning btn-sm" onclick="bulkAction('deactivate')">
                    <i class="bi bi-person-x me-1"></i>
                    Deactivate
                </button>
                <button class="btn btn-danger btn-sm" onclick="bulkAction('delete')">
                    <i class="bi bi-trash me-1"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <h5 class="card-title mb-0">
            <i class="bi bi-people me-2"></i>
            Customers List
        </h5>
        <div class="d-flex align-items-center gap-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll">
                <label class="form-check-label" for="selectAll">
                    Select All
                </label>
            </div>
            <small class="text-muted">
                Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} results
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
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Registration Date</th>
                        <th>Status</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr data-customer-id="{{ $customer->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input customer-checkbox" value="{{ $customer->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    @if($customer->profile_photo && file_exists(public_path('storage/' . $customer->profile_photo)))
                                        <img src="{{ asset('storage/' . $customer->profile_photo) }}" 
                                             alt="{{ $customer->name }}" 
                                             class="rounded-circle" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                             style="width: 40px; height: 40px; font-weight: 600;">
                                            {{ substr($customer->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $customer->name }}</h6>
                                    <small class="text-muted">ID: #{{ $customer->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-envelope me-2 text-muted"></i>
                                    <span>{{ $customer->email }}</span>
                                </div>
                                @if($customer->phone_number)
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-telephone me-2 text-muted"></i>
                                    <span>{{ $customer->country_code }} {{ $customer->phone_number }}</span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                @if($customer->customerProfile->city)
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-geo-alt me-2 text-muted"></i>
                                        <span>{{ $customer->customerProfile->city }}</span>
                                    </div>
                                @endif
                                @if($customer->customerProfile->country)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-flag me-2 text-muted"></i>
                                        <span>{{ $customer->customerProfile->country }}</span>
                                    </div>
                                @endif
                                @if(!$customer->customerProfile->city && !$customer->customerProfile->country)
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                <span>{{ $customer->created_at->format('M d, Y') }}</span>
                                <br>
                                <small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input status-toggle" 
                                       type="checkbox" 
                                       role="switch" 
                                       id="status{{ $customer->id }}" 
                                       data-customer-id="{{ $customer->id }}"
                                       {{ $customer->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="status{{ $customer->id }}">
                                    <span class="badge status-badge bg-{{ $customer->is_active ? 'success' : 'secondary' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                   class="btn btn-outline-info btn-sm"
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" 
                                   class="btn-edit btn-sm"
                                   title="Edit Customer">
                                    <i class="bi bi-pencil"></i>
                                    <span>Edit</span>
                                </a>
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm" 
                                        onclick="deleteCustomer({{ $customer->id }}, '{{ $customer->name }}')"
                                        title="Delete Customer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div>
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">No customers found</h5>
                                <p class="text-muted">Try adjusting your search criteria or add a new customer.</p>
                                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                                    <i class="bi bi-person-plus me-2"></i>
                                    Add First Customer
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($customers->hasPages())
    <div class="card-footer">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div>
                <small class="text-muted">
                    Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} results
                </small>
            </div>
            <div>
                {{ $customers->appends(request()->query())->links() }}
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
                <p>Are you sure you want to delete customer <strong id="customerName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone. All customer data, including payment methods, will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer gap-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash me-2"></i>
                    Delete Customer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentCustomerId = null;

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeEventListeners();
        handleCheckboxes(); // Initialize checkbox state
        setupFilterListeners(); // Setup filter change listeners
    });

    function initializeEventListeners() {
        // Select all functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const selectAllTableCheckbox = document.getElementById('selectAllTable');
        
        // Both select all checkboxes should work the same way
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                toggleAllCheckboxes(this.checked);
                if (selectAllTableCheckbox) {
                    selectAllTableCheckbox.checked = this.checked;
                }
            });
        }
        
        if (selectAllTableCheckbox) {
            selectAllTableCheckbox.addEventListener('change', function() {
                toggleAllCheckboxes(this.checked);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = this.checked;
                }
            });
        }

        // Individual checkbox listeners
        document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', handleCheckboxes);
        });

        // Status toggle listeners
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const customerId = this.getAttribute('data-customer-id');
                const isActive = this.checked;
                toggleCustomerStatus(customerId, isActive, this);
            });
        });

        // Delete confirmation listener
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (currentCustomerId) {
                    performDelete(currentCustomerId);
                }
            });
        }
    }

    function setupFilterListeners() {
        // Filters will only be applied when the Filter button is clicked
        // No auto-submission functionality
    }

    function toggleAllCheckboxes(checked) {
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        handleCheckboxes();
    }

    function handleCheckboxes() {
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        const selectAllTableCheckbox = document.getElementById('selectAllTable');
        const selectedCount = document.querySelectorAll('.customer-checkbox:checked').length;
        const bulkCard = document.getElementById('bulkActionsCard');
        
        // Update select all checkboxes
        const allChecked = selectedCount === checkboxes.length && checkboxes.length > 0;
        const someChecked = selectedCount > 0 && selectedCount < checkboxes.length;
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked;
        }
        
        if (selectAllTableCheckbox) {
            selectAllTableCheckbox.checked = allChecked;
            selectAllTableCheckbox.indeterminate = someChecked;
        }
        
        // Show/hide bulk actions
        if (selectedCount > 0) {
            bulkCard.style.display = 'block';
            document.getElementById('selectedCount').textContent = selectedCount;
        } else {
            bulkCard.style.display = 'none';
        }
    }

    // Toggle customer status
    function toggleCustomerStatus(customerId, isActive, toggleElement) {
        // Show loading state
        const originalDisabled = toggleElement.disabled;
        toggleElement.disabled = true;
        
        // Get the status badge
        const statusBadge = toggleElement.parentElement.querySelector('.status-badge');
        
        fetch(`/admin/customers/${customerId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_active: isActive })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update badge
                if (statusBadge) {
                    if (data.is_active) {
                        statusBadge.className = 'badge status-badge bg-success';
                        statusBadge.textContent = 'Active';
                    } else {
                        statusBadge.className = 'badge status-badge bg-secondary';
                        statusBadge.textContent = 'Inactive';
                    }
                }
                
                // Update stats
                updateStats(data.is_active ? 1 : -1, data.is_active ? -1 : 1);
                
                // Show success message
                showAlert('success', data.message || 'Customer status updated successfully');
            } else {
                // Revert toggle state
                toggleElement.checked = !isActive;
                showAlert('error', data.message || 'Failed to update customer status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert toggle state
            toggleElement.checked = !isActive;
            showAlert('error', 'An error occurred while updating status');
        })
        .finally(() => {
            toggleElement.disabled = originalDisabled;
        });
    }

    // Delete customer
    function deleteCustomer(customerId, customerName) {
        currentCustomerId = customerId;
        document.getElementById('customerName').textContent = customerName;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    function performDelete(customerId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/customers/${customerId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }

    // Bulk actions
    function bulkAction(action) {
        const selectedIds = Array.from(document.querySelectorAll('.customer-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            showAlert('warning', 'Please select customers first');
            return;
        }
        
        const actionText = action === 'activate' ? 'activate' : action === 'deactivate' ? 'deactivate' : 'delete';
        
        if (confirm(`Are you sure you want to ${actionText} ${selectedIds.length} customer(s)?`)) {
            // Perform bulk action
            fetch('/admin/customers/bulk-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: action,
                    ids: selectedIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    // Update stats based on the action and count
                    if (action === 'activate') {
                        updateStats(selectedIds.length, -selectedIds.length);
                    } else if (action === 'deactivate') {
                        updateStats(-selectedIds.length, selectedIds.length);
                    }
                    // Reload page after successful bulk action
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred during bulk action');
            });
        }
    }

    // Export customers
    function exportCustomers() {
        const btn = event.target.closest('button');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';
        btn.disabled = true;
        
        // Redirect to export route
        window.location.href = '{{ route("admin.customers.export") }}';
        
        // Reset button after a short delay
        setTimeout(() => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }, 1000);
    }

    // Update stats function
    function updateStats(activeChange, inactiveChange) {
        // Get current stats values
        const activeElement = document.querySelector('.stat-value.text-success');
        const inactiveElement = document.querySelector('.stat-value.text-warning');
        
        if (activeElement && inactiveElement) {
            // Update active count
            const currentActive = parseInt(activeElement.textContent) || 0;
            const newActive = Math.max(0, currentActive + activeChange);
            activeElement.textContent = newActive;
            
            // Update inactive count
            const currentInactive = parseInt(inactiveElement.textContent) || 0;
            const newInactive = Math.max(0, currentInactive + inactiveChange);
            inactiveElement.textContent = newInactive;
            
            // Add animation effect
            activeElement.classList.add('stat-updated');
            inactiveElement.classList.add('stat-updated');
            
            // Remove animation class after animation completes
            setTimeout(() => {
                activeElement.classList.remove('stat-updated');
                inactiveElement.classList.remove('stat-updated');
            }, 600);
        }
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
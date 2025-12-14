@extends('layouts.admin')

@section('title', 'Footer Pages Management')
@section('subtitle', 'Manage footer pages content and settings')

@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.footer-pages.social-media') }}" class="btn btn-outline-info">
            <i class="bi bi-share me-1"></i>
            Social Media
        </a>
    </div>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Total Pages</h6>
                        <h3 class="stat-value text-primary">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-primary">
                        <i class="bi bi-file-text"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Active Pages</h6>
                        <h3 class="stat-value text-success">{{ $stats['active'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-label">Inactive Pages</h6>
                        <h3 class="stat-value text-warning">{{ $stats['inactive'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-warning">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer Pages Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-file-text me-2"></i>
            Footer Pages
        </h5>
        <small class="text-muted">
            {{ $pages->total() }} total pages
        </small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Page Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Updated By</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="page-icon me-3">
                                        <i class="bi bi-file-text text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $page->title }}</h6>
                                        <small class="text-muted">{{ Str::limit(strip_tags($page->content), 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded">{{ $page->slug }}</code>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle" 
                                           type="checkbox" 
                                           {{ $page->is_active ? 'checked' : '' }}
                                           data-page-id="{{ $page->id }}"
                                           data-bs-toggle="tooltip"
                                           title="Toggle page status">
                                    <label class="form-check-label text-{{ $page->is_active ? 'success' : 'muted' }}">
                                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                @if($page->last_updated_by)
                                    <span data-bs-toggle="tooltip" title="{{ $page->last_updated_by->format('M d, Y g:i A') }}">
                                        {{ $page->last_updated_by->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                            <td>
                                @if($page->updatedByUser)
                                    <div class="d-flex align-items-center">
                                        @if($page->updatedByUser->profile_photo)
                                            <img src="{{ asset('storage/' . $page->updatedByUser->profile_photo) }}" 
                                                 alt="{{ $page->updatedByUser->name }}" 
                                                 class="rounded-circle me-2" 
                                                 width="24" height="24">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 24px; height: 24px;">
                                                <i class="bi bi-person text-white" style="font-size: 12px;"></i>
                                            </div>
                                        @endif
                                        <small>{{ $page->updatedByUser->name }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.footer-pages.show', $page) }}" 
                                       class="btn btn-sm btn-outline-info"
                                       data-bs-toggle="tooltip" title="View Page">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.footer-pages.edit', $page) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Edit Page">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-page"
                                            data-page-id="{{ $page->id }}"
                                            data-page-title="{{ $page->title }}"
                                            data-bs-toggle="tooltip" title="Delete Page">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-file-text text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3 text-muted">No Footer Pages Found</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($pages->hasPages())
        <div class="card-footer">
            {{ $pages->links() }}
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the page <strong id="deletePageTitle"></strong>?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Page</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Handle status toggle
    $('.status-toggle').change(function() {
        const pageId = $(this).data('page-id');
        const isChecked = $(this).is(':checked');
        const toggle = $(this);
        const label = toggle.next('label');
        
        // Disable toggle during request to prevent multiple clicks
        toggle.prop('disabled', true);
        
        $.ajax({
            url: `/admin/footer-pages/${pageId}/toggle-status`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update checkbox to match server response
                    toggle.prop('checked', response.is_active);
                    
                    // Update label text and styling
                    label.text(response.is_active ? 'Active' : 'Inactive');
                    label.removeClass('text-success text-muted');
                    label.addClass(response.is_active ? 'text-success' : 'text-muted');
                    
                    // Update statistics counters
                    updateStatistics(response.is_active, !isChecked);
                    
                    // Show success message
                    showAlert('success', response.message);
                } else {
                    // Revert toggle if server response indicates failure
                    toggle.prop('checked', !isChecked);
                    showAlert('danger', response.message || 'Failed to update page status.');
                }
            },
            error: function(xhr) {
                // Revert toggle on error
                toggle.prop('checked', !isChecked);
                let errorMessage = 'Failed to update page status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
            },
            complete: function() {
                // Re-enable toggle after request completes
                toggle.prop('disabled', false);
            }
        });
    });
    
    // Handle delete button click
    $('.delete-page').click(function() {
        const pageId = $(this).data('page-id');
        const pageTitle = $(this).data('page-title');
        
        $('#deletePageTitle').text(pageTitle);
        $('#deleteForm').attr('action', `/admin/footer-pages/${pageId}`);
        $('#deleteModal').modal('show');
    });
    
    // Helper function to show alerts
    function showAlert(type, message) {
        // Remove any existing alerts first
        $('.alert').remove();
        
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container-fluid').prepend(alertHtml);
        
        // Auto-dismiss after 4 seconds
        setTimeout(function() {
            $('.alert').fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }
    
    // Helper function to update statistics counters
    function updateStatistics(newStatus, oldStatus) {
        const activeCounter = $('.stat-label:contains("Active Pages")').next('.stat-value');
        const inactiveCounter = $('.stat-label:contains("Inactive Pages")').next('.stat-value');
        
        let activeCount = parseInt(activeCounter.text()) || 0;
        let inactiveCount = parseInt(inactiveCounter.text()) || 0;
        
        // Update counters based on status change
        if (newStatus && !oldStatus) {
            // Changed from inactive to active
            activeCount++;
            inactiveCount = Math.max(0, inactiveCount - 1);
        } else if (!newStatus && oldStatus) {
            // Changed from active to inactive
            activeCount = Math.max(0, activeCount - 1);
            inactiveCount++;
        }
        
        // Update the display
        activeCounter.text(activeCount);
        inactiveCounter.text(inactiveCount);
    }
});
</script>
@endsection
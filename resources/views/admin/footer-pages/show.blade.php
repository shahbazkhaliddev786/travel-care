@extends('layouts.admin')

@section('title', $footerPage->title)
@section('subtitle', 'Footer page details')

@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route($footerPage->route_name) }}" target="_blank" class="btn btn-outline-info">
            <i class="bi bi-box-arrow-up-right me-1"></i>
            View Live
        </a>
        <a href="{{ route('admin.footer-pages.edit', $footerPage) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>
            Edit Page
        </a>
        <a href="{{ route('admin.footer-pages.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Pages
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Page Content -->
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-text me-2"></i>
                    {{ $footerPage->title }}
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-{{ $footerPage->is_active ? 'success' : 'warning' }}">
                        <i class="bi bi-{{ $footerPage->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                        {{ $footerPage->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input status-toggle" 
                               type="checkbox" 
                               {{ $footerPage->is_active ? 'checked' : '' }}
                               data-page-id="{{ $footerPage->id }}"
                               data-bs-toggle="tooltip"
                               title="Toggle page status">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="page-content">
                    {!! $footerPage->content !!}
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small>
                            <i class="bi bi-link-45deg me-1"></i>
                            Slug: <code class="bg-light px-2 py-1 rounded">{{ $footerPage->slug }}</code>
                        </small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small>
                            <i class="bi bi-clock me-1"></i>
                            @if($footerPage->last_updated_by)
                                Updated {{ $footerPage->last_updated_by->diffForHumans() }}
                            @else
                                Created {{ $footerPage->created_at->diffForHumans() }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Page Information Sidebar -->
    <div class="col-xl-4 col-lg-5">
        <!-- Page Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Page Information
                </h6>
            </div>
            <div class="card-body">
                <div class="info-item mb-3">
                    <label class="form-label text-muted mb-1">Status</label>
                    <div>
                        <span class="badge bg-{{ $footerPage->is_active ? 'success' : 'warning' }} fs-6">
                            <i class="bi bi-{{ $footerPage->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                            {{ $footerPage->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                
                <div class="info-item mb-3">
                    <label class="form-label text-muted mb-1">Page URL</label>
                    <div>
                        <a href="{{ route($footerPage->route_name) }}" target="_blank" class="text-decoration-none">
                            {{ route($footerPage->route_name) }}
                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="info-item mb-3">
                    <label class="form-label text-muted mb-1">Slug</label>
                    <div>
                        <code class="bg-light px-2 py-1 rounded">{{ $footerPage->slug }}</code>
                    </div>
                </div>
                
                <div class="info-item mb-3">
                    <label class="form-label text-muted mb-1">Content Length</label>
                    <div>
                        <span class="badge bg-light text-dark">
                            {{ number_format(strlen(strip_tags($footerPage->content))) }} characters
                        </span>
                        <span class="badge bg-light text-dark ms-1">
                            {{ str_word_count(strip_tags($footerPage->content)) }} words
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Timeline -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Timeline
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @if($footerPage->last_updated_by && $footerPage->updatedByUser)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="d-flex align-items-center mb-1">
                                    @if($footerPage->updatedByUser->profile_photo)
                                        <img src="{{ asset('storage/' . $footerPage->updatedByUser->profile_photo) }}" 
                                             alt="{{ $footerPage->updatedByUser->name }}" 
                                             class="rounded-circle me-2" 
                                             width="24" height="24">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 24px; height: 24px;">
                                            <i class="bi bi-person text-white" style="font-size: 12px;"></i>
                                        </div>
                                    @endif
                                    <strong class="text-primary">{{ $footerPage->updatedByUser->name }}</strong>
                                </div>
                                <div class="text-muted small mb-1">Updated the page</div>
                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $footerPage->last_updated_by->format('M d, Y g:i A') }}
                                    <span class="ms-1">({{ $footerPage->last_updated_by->diffForHumans() }})</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <div class="d-flex align-items-center mb-1">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 24px; height: 24px;">
                                    <i class="bi bi-plus text-white" style="font-size: 12px;"></i>
                                </div>
                                <strong class="text-success">System</strong>
                            </div>
                            <div class="text-muted small mb-1">Page created</div>
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i>
                                {{ $footerPage->created_at->format('M d, Y g:i A') }}
                                <span class="ms-1">({{ $footerPage->created_at->diffForHumans() }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.footer-pages.edit', $footerPage) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>
                        Edit Page
                    </a>
                    
                    <a href="{{ $footerPage->route_name === 'footer.dynamic' ? route($footerPage->route_name, $footerPage->slug) : route($footerPage->route_name) }}" target="_blank" class="btn btn-outline-info">
                        <i class="bi bi-eye me-2"></i>
                        View Live Page
                    </a>
                    <button type="button"
                        class="btn btn-outline-{{ $footerPage->is_active ? 'warning' : 'success' }} toggle-status"
                        data-page-id="{{ $footerPage->id }}">
                        <i class="bi bi-{{ $footerPage->is_active ? 'x-circle' : 'check-circle' }} me-2"></i>
                        {{ $footerPage->is_active ? 'Deactivate' : 'Activate' }} Page
                    </button>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-page-id="{{ $footerPage->id }}"
                        data-page-title="{{ $footerPage->title }}">
                        <i class="bi bi-trash me-2"></i>
                        Delete Page
                    </button>
                </div>
            </div>
        </div>
    </div>
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

@section('styles')
<style>
.page-content {
    line-height: 1.6;
    font-size: 1rem;
}

.page-content h1, .page-content h2, .page-content h3, 
.page-content h4, .page-content h5, .page-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.page-content p {
    margin-bottom: 1rem;
}

.page-content ul, .page-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 12px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 4px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border-left: 3px solid #dee2e6;
}

.info-item {
    border-bottom: 1px solid #f1f3f4;
    padding-bottom: 0.75rem;
}

.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.stat-card {
    transition: transform 0.2s ease-in-out;
}

.stat-card:hover {
    transform: translateY(-2px);
}
</style>
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
    $('.status-toggle, .toggle-status').on('change click', function(e) {
        e.preventDefault();
        
        const pageId = $(this).data('page-id');
        const isToggle = $(this).hasClass('status-toggle');
        
        $.ajax({
            url: `/admin/footer-pages/${pageId}/toggle-status`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Reload page to update all status indicators
                    location.reload();
                }
            },
            error: function() {
                showAlert('danger', 'Failed to update page status.');
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
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container-fluid').prepend(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>
@endsection
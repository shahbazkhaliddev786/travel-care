@extends('layouts.admin')

@section('title', 'Social Media Links')
@section('subtitle', 'Manage footer social media links')

@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.footer-pages.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Pages
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLinkModal">
            <i class="bi bi-plus-circle me-1"></i>
            Add Link
        </button>
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
                        <h6 class="stat-label">Total Links</h6>
                        <h3 class="stat-value text-primary">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="stat-icon text-primary">
                        <i class="bi bi-share"></i>
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
                        <h6 class="stat-label">Active Links</h6>
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
                        <h6 class="stat-label">Inactive Links</h6>
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

<!-- Social Media Links -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-share me-2"></i>
            Social Media Links
        </h5>
        <small class="text-muted">
            {{ $links->count() }} total links
        </small>
    </div>
    <div class="card-body p-0">
        @if($links->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <i class="bi bi-grip-vertical text-muted" data-bs-toggle="tooltip" title="Drag to reorder"></i>
                            </th>
                            <th>Platform</th>
                            <th>URL</th>
                            <th>Icon</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-links">
                        @foreach($links as $link)
                            <tr data-link-id="{{ $link->id }}">
                                <td class="text-center">
                                    <i class="bi bi-grip-vertical text-muted drag-handle" style="cursor: move;"></i>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="platform-icon me-3">
                                            <i class="{{ $link->icon }} text-primary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $link->formatted_platform }}</h6>
                                            <small class="text-muted">{{ $link->platform }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ $link->url }}" target="_blank" class="text-decoration-none">
                                        {{ Str::limit($link->url, 40) }}
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $link->icon_class }}</code>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" 
                                               type="checkbox" 
                                               {{ $link->is_active ? 'checked' : '' }}
                                               data-link-id="{{ $link->id }}"
                                               data-bs-toggle="tooltip"
                                               title="Toggle link status">
                                        <label class="form-check-label text-{{ $link->is_active ? 'success' : 'muted' }}">
                                            {{ $link->is_active ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $link->sort_order }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary edit-link"
                                                data-link-id="{{ $link->id }}"
                                                data-platform="{{ $link->platform }}"
                                                data-url="{{ $link->url }}"
                                                data-icon-class="{{ $link->icon_class }}"
                                                data-is-active="{{ $link->is_active }}"
                                                data-bs-toggle="tooltip" title="Edit Link">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-link"
                                                data-link-id="{{ $link->id }}"
                                                data-platform="{{ $link->formatted_platform }}"
                                                data-bs-toggle="tooltip" title="Delete Link">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="bi bi-share text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No Social Media Links Found</h5>
                    <p class="text-muted">Add your first social media link to get started.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLinkModal">
                        <i class="bi bi-plus-circle me-1"></i>
                        Add Social Media Link
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Add Link Modal -->
<div class="modal fade" id="addLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Social Media Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addLinkForm" action="{{ route('admin.footer-pages.social-media.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_platform" class="form-label">Platform <span class="text-danger">*</span></label>
                        <select class="form-select" id="add_platform" name="platform" required>
                            <option value="">Select Platform</option>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="twitter">Twitter</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="youtube">YouTube</option>
                            <option value="tiktok">TikTok</option>
                            <option value="pinterest">Pinterest</option>
                            <option value="snapchat">Snapchat</option>
                            <option value="telegram">Telegram</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_url" class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="add_url" name="url" placeholder="https://" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_icon_class" class="form-label">Icon Class</label>
                        <input type="text" class="form-control" id="add_icon_class" name="icon_class" placeholder="bi bi-facebook">
                        <div class="form-text">Bootstrap Icons class (e.g., bi bi-facebook). Leave empty for auto-detection.</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="add_is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="add_is_active">
                                <strong>Active</strong>
                            </label>
                            <div class="form-text">Enable this link to be visible on the website.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Link Modal -->
<div class="modal fade" id="editLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Social Media Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editLinkForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_platform" class="form-label">Platform <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_platform" name="platform" required>
                            <option value="">Select Platform</option>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="twitter">Twitter</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="youtube">YouTube</option>
                            <option value="tiktok">TikTok</option>
                            <option value="pinterest">Pinterest</option>
                            <option value="snapchat">Snapchat</option>
                            <option value="telegram">Telegram</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_url" class="form-label">URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="edit_url" name="url" placeholder="https://" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_icon_class" class="form-label">Icon Class</label>
                        <input type="text" class="form-control" id="edit_icon_class" name="icon_class" placeholder="bi bi-facebook">
                        <div class="form-text">Bootstrap Icons class (e.g., bi bi-facebook). Leave empty for auto-detection.</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">
                                <strong>Active</strong>
                            </label>
                            <div class="form-text">Enable this link to be visible on the website.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Link</button>
                </div>
            </form>
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
                <p>Are you sure you want to delete the <strong id="deletePlatform"></strong> link?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Link</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize sortable
    const sortableElement = document.getElementById('sortable-links');
    if (sortableElement) {
        new Sortable(sortableElement, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                updateOrder();
            }
        });
    }
    
    // Handle status toggle
    $('.status-toggle').change(function() {
        const linkId = $(this).data('link-id');
        const isChecked = $(this).is(':checked');
        const toggle = $(this);
        const label = toggle.next('label');
        
        // Disable toggle during request to prevent multiple clicks
        toggle.prop('disabled', true);
        
        $.ajax({
            url: `/admin/footer-pages/social-media/${linkId}/toggle-status`,
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
                    
                    showAlert('success', response.message);
                } else {
                    // Revert toggle if server response indicates failure
                    toggle.prop('checked', !isChecked);
                    showAlert('danger', response.message || 'Failed to update link status.');
                }
            },
            error: function(xhr) {
                // Revert toggle on error
                toggle.prop('checked', !isChecked);
                let errorMessage = 'Failed to update link status.';
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
    
    // Handle edit button click
    $('.edit-link').click(function() {
        const linkId = $(this).data('link-id');
        const platform = $(this).data('platform');
        const url = $(this).data('url');
        const iconClass = $(this).data('icon-class');
        const isActive = $(this).data('is-active');
        
        $('#edit_platform').val(platform);
        $('#edit_url').val(url);
        $('#edit_icon_class').val(iconClass);
        $('#edit_is_active').prop('checked', isActive);
        $('#editLinkForm').attr('action', `/admin/footer-pages/social-media/${linkId}`);
        
        $('#editLinkModal').modal('show');
    });
    
    // Handle delete button click
    $('.delete-link').click(function() {
        const linkId = $(this).data('link-id');
        const platform = $(this).data('platform');
        
        $('#deletePlatform').text(platform);
        $('#deleteForm').attr('action', `/admin/footer-pages/social-media/${linkId}`);
        $('#deleteModal').modal('show');
    });
    
    // Update order function
    function updateOrder() {
        const order = [];
        $('#sortable-links tr').each(function(index) {
            const linkId = $(this).data('link-id');
            if (linkId) {
                order.push({
                    id: linkId,
                    sort_order: index + 1
                });
            }
        });
        
        $.ajax({
            url: '{{ route("admin.footer-pages.social-media.update-order") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { links: order },
            success: function(response) {
                if (response.success) {
                    // Update order badges
                    $('#sortable-links tr').each(function(index) {
                        $(this).find('.badge').text(index + 1);
                    });
                    
                    showAlert('success', 'Link order updated successfully.');
                }
            },
            error: function() {
                showAlert('danger', 'Failed to update link order.');
            }
        });
    }
    
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
        const activeCounter = $('.stat-label:contains("Active Links")').next('.stat-value');
        const inactiveCounter = $('.stat-label:contains("Inactive Links")').next('.stat-value');
        
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
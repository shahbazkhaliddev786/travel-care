@extends('layouts.admin')

@section('title', 'Edit Footer Page')
@section('subtitle', 'Modify footer page content')

@section('actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.footer-pages.show', $footerPage) }}" class="btn btn-outline-info">
            <i class="bi bi-eye me-1"></i>
            View Page
        </a>
        <a href="{{ route('admin.footer-pages.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Pages
        </a>
    </div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Footer Page
                </h5>
                <div class="d-flex align-items-center text-muted">
                    <small>
                        <i class="bi bi-clock me-1"></i>
                        Last updated: 
                        @if($footerPage->last_updated_by)
                            {{ $footerPage->last_updated_by->format('M d, Y g:i A') }}
                        @else
                            Never
                        @endif
                    </small>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.footer-pages.update', $footerPage) }}" method="POST" id="editPageForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Page Title <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $footerPage->title) }}"
                                       placeholder="Enter page title"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">This will be displayed as the page heading.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Page Slug <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" 
                                       name="slug" 
                                       value="{{ old('slug', $footerPage->slug) }}"
                                       placeholder="page-slug"
                                       required>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">URL-friendly identifier for the page.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Page Content <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" 
                                  name="content" 
                                  rows="15"
                                  placeholder="Enter page content..."
                                  required>{{ old('content', $footerPage->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Use the Summernote editor to format your content with bold, italic, underline, links, tables, and more.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', $footerPage->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active</strong>
                                    </label>
                                    <div class="form-text">Enable this page to be visible on the website.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            @if($footerPage->updatedByUser)
                                <div class="mb-3">
                                    <label class="form-label">Last Updated By</label>
                                    <div class="d-flex align-items-center">
                                        @if($footerPage->updatedByUser->profile_photo)
                                            <img src="{{ asset('storage/' . $footerPage->updatedByUser->profile_photo) }}" 
                                                 alt="{{ $footerPage->updatedByUser->name }}" 
                                                 class="rounded-circle me-2" 
                                                 width="32" height="32">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="bi bi-person text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $footerPage->updatedByUser->name }}</div>
                                            <small class="text-muted">{{ $footerPage->updatedByUser->email }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <small>
                                <i class="bi bi-info-circle me-1"></i>
                                Fields marked with <span class="text-danger">*</span> are required.
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.footer-pages.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>
                                Cancel
                            </a>
                            <button type="button" class="btn btn-outline-info" id="togglePreview">
                                <i class="bi bi-eye me-1"></i>
                                Show Preview
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Update Page
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Preview Card -->
        <div class="card mt-4" id="previewCard" style="display: none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-eye me-2"></i>
                    Live Preview
                </h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="hidePreview">
                    <i class="bi bi-eye-slash"></i>
                    Hide Preview
                </button>
            </div>
            <div class="card-body">
                <div class="preview-content">
                    <h1 id="previewTitle">{{ $footerPage->title }}</h1>
                    <hr>
                    <div id="previewContent">{!! $footerPage->content !!}</div>
                </div>
            </div>
        </div>
        
        <!-- Page History Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Page Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label text-muted">Created At</label>
                            <div>{{ $footerPage->created_at->format('M d, Y g:i A') }}</div>
                        <small class="text-muted">{{ $footerPage->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label text-muted">Last Updated</label>
                            <div>
                                @if($footerPage->last_updated_by)
                                    {{ $footerPage->last_updated_by->format('M d, Y g:i A') }}
                                @else
                                    Never updated
                                @endif
                            </div>
                            <small class="text-muted">
                                @if($footerPage->last_updated_by)
                                    {{ $footerPage->last_updated_by->diffForHumans() }}
                                @else
                                    Original content
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label text-muted">Page URL</label>
                            <div>
                                <a href="{{ $footerPage->route_name === 'footer.dynamic' ? route($footerPage->route_name, $footerPage->slug) : route($footerPage->route_name) }}" target="_blank" class="text-decoration-none">
                                    {{ $footerPage->route_name === 'footer.dynamic' ? route($footerPage->route_name, $footerPage->slug) : route($footerPage->route_name) }}
                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                <span class="badge bg-{{ $footerPage->is_active ? 'success' : 'warning' }}">
                                    <i class="bi bi-{{ $footerPage->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ $footerPage->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Summernote CSS for Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">

<!-- Custom CSS for Summernote styles -->
<style>
    .note-editor .note-editable h1 { font-size: 2.5rem; font-weight: bold; margin: 0.5rem 0; color: #333; }
    .note-editor .note-editable h2 { font-size: 2rem; font-weight: bold; margin: 0.5rem 0; color: #333; }
    .note-editor .note-editable h3 { font-size: 1.75rem; font-weight: bold; margin: 0.5rem 0; color: #333; }
    .note-editor .note-editable h4 { font-size: 1.5rem; font-weight: bold; margin: 0.5rem 0; color: #333; }
    .note-editor .note-editable h5 { font-size: 1.25rem; font-weight: bold; margin: 0.5rem 0; color: #333; }
    .note-editor .note-editable h6 { font-size: 1rem; font-weight: bold; margin: 0.5rem 0; color: #333; }
    .note-editor .note-editable blockquote { 
        padding: 0.75rem 1rem; 
        margin: 0.5rem 0; 
        border-left: 4px solid #007bff; 
        background-color: #f8f9fa; 
        font-style: italic; 
        color: #6c757d;
    }
    .note-editor .note-editable pre { 
        background-color: #f8f9fa; 
        padding: 0.75rem; 
        border: 1px solid #dee2e6; 
        border-radius: 0.25rem; 
        font-family: 'Courier New', monospace; 
        color: #495057;
    }
    
    .note-toolbar {
        border-radius: 16px 16px 0 0;
        display: flex;
        flex-wrap: wrap;
    }
    
    .note-btn {
        background: white;
    }

    .btn-group .note-btn .note-current-fontname,
    .btn-group .note-btn .note-current-fontsize {
        display: block;
    }

    /* Bootstrap 5 specific dropdown fixes */
    .note-toolbar .dropdown-toggle {
        border: 1px solid #dee2e6;
        color: #495057;
    }
    
    .note-toolbar .dropdown-toggle:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
    }
    
    .note-toolbar .dropdown-menu {
        background: #fff;
        width: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .note-toolbar .dropdown-item {
        padding: 0.5rem 1rem;
        color: #495057;
    }
    
    .note-toolbar .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #495057;
    }
    
    /* Fix popup modal styling issues */
    .note-modal .modal-dialog {
        width: 600px !important; /* Increased width for better appearance */
        max-width: 90vw;
        margin: 30px auto;
        box-shadow: none !important; /* Remove any box-shadow from dialog */
    }
    
    /* Ensure no conflicting shadows from other elements */
    .note-modal .modal-dialog::before,
    .note-modal .modal-dialog::after {
        display: none !important;
    }
    
    /* Override any Bootstrap or Summernote modal shadows */
    .modal.note-modal .modal-dialog {
        box-shadow: none !important;
    }
    
    .modal.note-modal {
        box-shadow: none !important;
    }
    
    /* Ensure only the content has shadow */
    .note-modal .modal-content {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        -webkit-box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        -moz-box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    /* Fix Insert Link button visibility */
    .note-modal .modal-footer .btn-primary,
    .note-link-dialog .modal-footer .btn-primary {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: white !important;
        opacity: 1 !important; /* Always visible */
        visibility: visible !important;
    }
    
</style>

<!-- Popper.js (required for Bootstrap 5 dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<!-- Summernote JS for Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Summernote with Bootstrap 5 compatibility
    $('#content').summernote({
        height: 400,
        minHeight: 200,
        maxHeight: 800,
        focus: false,
        placeholder: 'Enter your content here...',
        tabsize: 2,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['forecolor', 'backcolor']],
            ['para', ['ul', 'ol', 'paragraph', 'height']],
            ['table', ['table']],
            ['insert', ['link', 'hr']],
            ['view', ['help']]
        ],
        fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica Neue', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 'Times New Roman', 'Verdana'],
        fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '36', '48'],
        styleTags: ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        styleWithSpan: false,
        // Bootstrap 5 specific configurations
        popover: {
            air: [
                ['color', ['color']],
                ['font', ['bold', 'underline', 'clear']]
            ]
        },
        colors: [
            ['#000000', '#424242', '#636363', '#9C9C94', '#CEC6CE', '#EFEFEF', '#F7F3F7', '#FFFFFF'],
            ['#FF0000', '#FF9C00', '#FFFF00', '#00FF00', '#00FFFF', '#0000FF', '#9C00FF', '#FF00FF'],
            ['#F7C6CE', '#FFE7CE', '#FFEFC6', '#D6EFD6', '#CEDEE7', '#CEE7F7', '#D6D6E7', '#E7D6DE'],
            ['#E79C9C', '#FFC69C', '#FFE79C', '#B5D6A5', '#A5C6CE', '#9CC6EF', '#B5A5D6', '#D6A5BD'],
            ['#E76363', '#F7AD6B', '#FFD663', '#94BD7B', '#73A5AD', '#6BADDE', '#8C7BC6', '#C67BA5'],
            ['#CE0000', '#E79439', '#EFC631', '#6BA54A', '#4A7B8C', '#3984C6', '#634AA5', '#A54A7B'],
            ['#9C0000', '#B56308', '#BD9400', '#397B21', '#104A5A', '#085294', '#311873', '#731842'],
            ['#630000', '#7B3900', '#846300', '#295218', '#083139', '#003163', '#21104A', '#4A1031']
        ],
        dialogsInBody: true,
        dialogsFade: true,
        disableDragAndDrop: false,
        shortcuts: true,
        callbacks: {
            onChange: function(contents, $editable) {
                updatePreview();
            },
            onImageUpload: function(files) {
                // Handle image upload
                for (let i = 0; i < files.length; i++) {
                    uploadImage(files[i]);
                }
            },
            onInit: function() {
                // Ensure Bootstrap 5 dropdowns are properly initialized
                console.log('Summernote initialized with Bootstrap 5 compatibility');
                
                // Initialize Bootstrap 5 dropdowns manually if needed
                setTimeout(function() {
                    $('.note-toolbar .dropdown-toggle').each(function() {
                        if (!$(this).hasClass('dropdown-initialized')) {
                            $(this).addClass('dropdown-initialized');
                            
                            // Ensure proper Bootstrap 5 dropdown behavior
                            $(this).on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                // Close other dropdowns
                                $('.note-toolbar .dropdown-menu').not($(this).next('.dropdown-menu')).removeClass('show');
                                
                                // Toggle current dropdown
                                $(this).next('.dropdown-menu').toggleClass('show');
                            });
                        }
                    });
                    
                    // Close dropdowns when clicking outside
                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('.note-toolbar .dropdown').length) {
                            $('.note-toolbar .dropdown-menu').removeClass('show');
                        }
                    });
                }, 100);
            }
        }
    });
    
    // Function to handle image upload
    function uploadImage(file) {
        let data = new FormData();
        data.append('file', file);
        data.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // For now, we'll use a placeholder since we don't have an upload endpoint
        // You can implement this later with a proper image upload route
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#content').summernote('insertImage', e.target.result);
        };
        reader.readAsDataURL(file);
    }
    // Auto-generate slug from title (only if title changes)
    let originalTitle = $('#title').val();
    $('#title').on('input', function() {
        const title = $(this).val();
        if (title !== originalTitle) {
            const slug = generateSlug(title);
            $('#slug').val(slug);
        }
        updatePreview();
    });
    
    // Update preview when content changes
    $('#content').on('input', function() {
        updatePreview();
    });
    
    // Show/hide preview
    $('#togglePreview').click(function() {
        $('#previewCard').slideDown();
        updatePreview();
        $(this).hide();
    });
    
    $('#hidePreview').click(function() {
        $('#previewCard').slideUp();
        $('#togglePreview').show();
    });
    
    // Form validation
    $('#editPageForm').on('submit', function(e) {
        const title = $('#title').val().trim();
        const slug = $('#slug').val().trim();
        
        // Get content from Summernote if initialized
        let content = '';
        if ($('#content').hasClass('note-editable') || $('#content').next('.note-editor').length > 0) {
            content = $('#content').summernote('code').trim();
        } else {
            content = $('#content').val().trim();
        }
        
        if (!title || !slug || !content) {
            e.preventDefault();
            showAlert('danger', 'Please fill in all required fields.');
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="bi bi-hourglass-split me-1"></i> Updating...');
    });
    
    // Generate slug function
    function generateSlug(text) {
        return text
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '') // Remove special characters
            .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
            .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
    }
    
    // Update preview function
    function updatePreview() {
        const title = $('#title').val() || 'Page Title';
        let content = 'Page content will appear here...';
        
        // Get content from Summernote if initialized
        if ($('#content').hasClass('note-editable') || $('#content').next('.note-editor').length > 0) {
            content = $('#content').summernote('code') || 'Page content will appear here...';
        } else {
            content = $('#content').val() || 'Page content will appear here...';
        }
        
        $('#previewTitle').text(title);
        $('#previewContent').html(content);
    }
    
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
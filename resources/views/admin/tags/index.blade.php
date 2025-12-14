@extends('layouts.admin')

@section('title', 'Tag Management')

@section('actions')
<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createTagModal">
    <i class="bi bi-plus-lg"></i> Create New Tag
</button>
@endsection

@section('content')
<!-- Search and Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Search & Filter</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.tags.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name...">
            </div>
            <div class="col-md-3">
                <label for="sort" class="form-label">Sort By</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    <option value="usage_asc" {{ request('sort') == 'usage_asc' ? 'selected' : '' }}>Usage (Low-High)</option>
                    <option value="usage_desc" {{ (request('sort') == 'usage_desc' || !request('sort')) ? 'selected' : '' }}>Usage (High-Low)</option>
                    <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Created (Oldest)</option>
                    <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Created (Newest)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tags List -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">All Tags</h6>
    </div>
    <div class="card-body">
        @if(count($tags) > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Usage Count</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                            <tr>
                                <td>{{ $tag->name }}</td>
                                <td>{{ $tag->description ?? 'No description' }}</td>
                                <td>{{ $tag->services_count }}</td>
                                <td>{{ $tag->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.tags.edit', $tag->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete({{ $tag->id }}, '{{ $tag->name }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $tags->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <p>No tags found.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTagModal">
                    <i class="bi bi-plus-lg"></i> Create First Tag
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Create Tag Modal -->
<div class="modal fade" id="createTagModal" tabindex="-1" aria-labelledby="createTagModalLabel" aria-hidden="true">
    <div class="modal-dialog mx-auto">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTagModalLabel">Create New Tag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.tags.store') }}" method="POST" id="createTagModalForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_name" class="form-label">Tag Name</label>
                        <input type="text" class="form-control" id="modal_name" name="name" required maxlength="50">
                        <div class="form-text">Enter a unique name for the tag (max 50 characters)</div>
                    </div>
                    <div class="mb-3">
                        <label for="modal_description" class="form-label">Description</label>
                        <textarea class="form-control" id="modal_description" name="description" rows="3" maxlength="255"></textarea>
                        <div class="form-text">Optional description for the tag (max 255 characters)</div>
                    </div>
                </div>
                <div class="modal-footer gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="createTagModalButton">Create Tag</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteTagModal" tabindex="-1" aria-labelledby="deleteTagModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTagModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the tag <strong id="delete_tag_name"></strong>?</p>
                <p class="text-danger">This action cannot be undone and will remove this tag from all associated services.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteTagForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Tag</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle modal form submission
        const modalForm = document.getElementById('createTagModalForm');
        const modalButton = document.getElementById('createTagModalButton');
        
        if (modalForm && modalButton) {
            modalForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const nameInput = document.getElementById('modal_name');
                const descriptionInput = document.getElementById('modal_description');
                
                // Validate form data
                if (!nameInput.value.trim()) {
                    alert('Please enter a tag name.');
                    nameInput.focus();
                    return false;
                }
                
                if (nameInput.value.trim().length > 50) {
                    alert('Tag name must be 50 characters or less.');
                    nameInput.focus();
                    return false;
                }

                // Show loading state
                modalButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Creating...';
                modalButton.disabled = true;
                
                // Create FormData object
                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('name', nameInput.value.trim());
                formData.append('description', descriptionInput.value.trim());
                
                // Submit via AJAX
                fetch(modalForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal and refresh page
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createTagModal'));
                        modal.hide();
                        
                        // Show success message
                        alert('Tag created successfully!');
                        
                        // Refresh the page to show the new tag
                        window.location.reload();
                    } else {
                        // Show error message
                        alert(data.message || 'An error occurred while creating the tag.');
                        
                        // Reset button
                        modalButton.innerHTML = 'Create Tag';
                        modalButton.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while creating the tag. Please try again.');
                    
                    // Reset button
                    modalButton.innerHTML = 'Create Tag';
                    modalButton.disabled = false;
                });
                
                return false;
            });
            
            // Prevent admin layout interference with modal buttons
            modalButton.addEventListener('click', function(e) {
                e.stopImmediatePropagation();
            });
        }
    });


    // Delete confirmation function
    function confirmDelete(id, name) {
        document.getElementById('delete_tag_name').textContent = name;
        document.getElementById('deleteTagForm').action = `{{ url('admin/tags') }}/${id}`;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteTagModal'));
        deleteModal.show();
    }
</script>
@endsection
@extends('layouts.admin')

@section('title', 'Edit Tag')

@section('actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.tags.index') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Tags
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <!-- Edit Tag Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Edit Tag</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Tag Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $tag->name }}" required>
                        @error('name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $tag->description }}</textarea>
                        @error('description')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary" id="submitButton">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTagModal">
                            <i class="bi bi-trash"></i> Delete Tag
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <!-- Tag Usage Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Tag Usage</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5>Usage Statistics</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body py-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Total Services</div>
                                            <div class="h5 mb-0 font-weight-bold text-white">{{ $tag->services_count }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="bi bi-list-check fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body py-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Usage Rank</div>
                                            <div class="h5 mb-0 font-weight-bold text-white">#{{ $tagRank }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="bi bi-bar-chart-line fs-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5>Provider Distribution</h5>
                    <div class="chart-pie mb-4">
                        <canvas id="providerDistributionChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-primary"></i> Doctors
                        </span>
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-success"></i> Hospitals
                        </span>
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-info"></i> Clinics
                        </span>
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-warning"></i> Laboratories
                        </span>
                    </div>
                </div>
                
                <div>
                    <h5>Related Tags</h5>
                    @if(count($relatedTags) > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($relatedTags as $relatedTag)
                                <a href="{{ route('admin.tags.edit', $relatedTag->id) }}" class="badge bg-secondary p-2 text-decoration-none">
                                    {{ $relatedTag->name }} ({{ $relatedTag->common_services }})
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p>No related tags found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Delete Tag Modal -->
<div class="modal fade" id="deleteTagModal" tabindex="-1" aria-labelledby="deleteTagModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTagModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the tag <strong>{{ $tag->name }}</strong>?</p>
                @if($tag->services_count > 0)
                    <p class="text-danger">This tag is currently used by {{ $tag->services_count }} services. Deleting it will remove the tag from all these services.</p>
                @endif
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST">
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const submitButton = document.getElementById('submitButton');
        const editForm = document.querySelector('form');
        
        // Prevent the admin layout's button loading interference
        if (submitButton) {
            submitButton.addEventListener('click', function(e) {
                // Stop the event from bubbling up to the admin layout's handler
                e.stopImmediatePropagation();
            });
        }

        // Form validation and submission
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                // Validate form data first
                const nameInput = document.getElementById('name');
                const descriptionInput = document.getElementById('description');
                
                if (!nameInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter a tag name.');
                    nameInput.focus();
                    return false;
                }
                
                if (nameInput.value.trim().length > 50) {
                    e.preventDefault();
                    alert('Tag name must be 50 characters or less.');
                    nameInput.focus();
                    return false;
                }

                // Show loading state
                if (submitButton) {
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
                    submitButton.disabled = true;
                }
                
                // Allow form to submit normally - no need for AJAX here since it's a dedicated page
                return true;
            });
        }
    });


    
    // Provider Distribution Chart
    const providerDistributionCtx = document.getElementById('providerDistributionChart').getContext('2d');
    const providerDistributionChart = new Chart(providerDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Doctors', 'Hospitals', 'Clinics', 'Laboratories'],
            datasets: [{
                data: [
                    {{ $providerDistribution['doctors'] ?? 0 }},
                    {{ $providerDistribution['hospitals'] ?? 0 }},
                    {{ $providerDistribution['clinics'] ?? 0 }},
                    {{ $providerDistribution['laboratories'] ?? 0 }}
                ],
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e'
                ],
                hoverBackgroundColor: [
                    '#2e59d9',
                    '#17a673',
                    '#2c9faf',
                    '#dda20a'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection
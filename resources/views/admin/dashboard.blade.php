@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Welcome to TravelCare Admin Panel')

@section('actions')
<div class="d-flex gap-3">
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>
        Add Customer
    </a>
</div>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card customers h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-2">Total Customers</p>
                        <h2 class="stat-value mb-1">{{ $totalCustomers }}</h2>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success-subtle text-success me-2">
                                <i class="bi bi-arrow-up-short"></i>
                                12%
                            </span>
                            <small class="text-muted">vs last month</small>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card doctors h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-2">Active Doctors</p>
                        <h2 class="stat-value mb-1">{{ $activeDoctors }}</h2>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-info-subtle text-info me-2">
                                <i class="bi bi-arrow-up-short"></i>
                                8%
                            </span>
                            <small class="text-muted">vs last month</small>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card consultations h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-2">Total Hospitals</p>
                        <h2 class="stat-value mb-1">{{ $totalHospitals }}</h2>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning-subtle text-warning me-2">
                                <i class="bi bi-arrow-up-short"></i>
                                15%
                            </span>
                            <small class="text-muted">vs last month</small>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stat-card users h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label mb-2">Pending Approvals</p>
                        <h2 class="stat-value mb-1">{{ $pendingDoctors }}</h2>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger-subtle text-danger me-2">
                                <i class="bi bi-exclamation-triangle"></i>
                                Action Required
                            </span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-star me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('admin.customers.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 bg-primary-subtle rounded hover-shadow">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-people text-white fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-primary">Manage Customers</h6>
                                    <small class="text-muted">View, edit, and manage customer accounts</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('admin.doctors.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 bg-info-subtle rounded hover-shadow">
                                <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-3" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-heart-pulse text-white fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-info">Doctor Approvals</h6>
                                    <small class="text-muted">Review and approve doctor registrations</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('admin.hospitals.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 bg-success-subtle rounded hover-shadow">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-3" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-building text-white fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-success">Hospitals</h6>
                                    <small class="text-muted">Manage hospital listings and information</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('admin.tags.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center p-3 bg-warning-subtle rounded hover-shadow">
                                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center me-3" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-tags text-white fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-warning">Tags & Services</h6>
                                    <small class="text-muted">Manage service categories and tags</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popular Services -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-star me-2"></i>
                    Most Requested Services
                </h5>
                                        <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-primary btn-sm">
                    View All Tags
                </a>
            </div>
            <div class="card-body">
                @if(isset($popularTags) && count($popularTags) > 0)
                    <div class="row">
                        @foreach($popularTags as $index => $tag)
                            <div class="col-lg-6 mb-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="badge bg-primary rounded-pill me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $tag->name }}</h6>
                                        <small class="text-muted">Used in {{ $tag->services_count }} services</small>
                                    </div>
                                    <div class="progress" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: {{ min(($tag->services_count / max($popularTags->max('services_count'), 1)) * 100, 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-tags text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No service tags yet</h5>
                        <p class="text-muted">Service tags will appear here once doctors start adding services.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="recent-activity row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>
                    Recent Doctors
                </h5>
                <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-primary btn-sm">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if(isset($recentDoctors) && count($recentDoctors) > 0)
                    @foreach($recentDoctors as $doctor)
                        <div class="d-flex align-items-center mb-3 p-2 hover-bg-light rounded">
                            <div class="avatar me-3">
                                @if($doctor->profile_image)
                                    <img src="{{ asset('storage/' . $doctor->profile_image) }}" 
                                         alt="{{ $doctor->name }}" 
                                         class="rounded-circle" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                         style="width: 40px; height: 40px;">
                                        {{ substr($doctor->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $doctor->name }}</h6>
                                <p class="mb-0 text-muted small">{{ $doctor->email }}</p>
                                <small class="text-muted">{{ $doctor->created_at->diffForHumans() }}</small>
                            </div>
                            <div>
                                @if($doctor->is_verified)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-heart-pulse text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No recent doctors</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>
                    Recent Customers
                </h5>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary btn-sm">
                    View All
                </a>
            </div>
            <div class="card-body">
                @if(isset($recentCustomers) && count($recentCustomers) > 0)
                    @foreach($recentCustomers as $customer)
                        <div class="d-flex align-items-center mb-3 p-2 hover-bg-light rounded">
                            <div class="avatar me-3">
                                @if($customer->profile_photo && file_exists(public_path('storage/' . $customer->profile_photo)))
                                    <img src="{{ asset('storage/' . $customer->profile_photo) }}" 
                                         alt="{{ $customer->name }}" 
                                         class="rounded-circle" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white" 
                                         style="width: 40px; height: 40px;">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $customer->name }}</h6>
                                <p class="mb-0 text-muted small">{{ $customer->email }}</p>
                                <small class="text-muted">{{ $customer->created_at->diffForHumans() }}</small>
                            </div>
                            <div>
                                @if($customer->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No recent customers</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .hover-bg-light:hover {
        background-color: rgba(0, 173, 173, 0.05) !important;
    }
    
    .bg-primary-subtle {
        background-color: rgba(0, 173, 173, 0.1) !important;
    }
    
    .bg-info-subtle {
        background-color: rgba(23, 162, 184, 0.1) !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
    
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    
    .text-primary { color: #00ADAD !important; }
    .text-info { color: #17a2b8 !important; }
    .text-success { color: #28a745 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-danger { color: #dc3545 !important; }
</style>

<script>
    // Show alert function
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 3000);
    }

    // Add hover effects to cards
    document.addEventListener('DOMContentLoaded', function() {
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>

<script src="{{ asset('js/admin.js') }}"></script>
@endsection
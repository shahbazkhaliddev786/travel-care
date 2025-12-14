@extends('layouts.admin')

@section('title', 'Customer Details')

@section('actions')
<div class="d-flex gap-2">
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Customers
    </a>
    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary">
        <i class="bi bi-pencil me-2"></i>
        Edit Customer
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4 left-col">
        <!-- Customer Profile Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Customer Profile</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($customer->profile_photo)
                        <img src="{{ asset('storage/' . $customer->profile_photo) }}" alt="{{ $customer->name }}" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white mx-auto" 
                             style="width: 150px; height: 150px; font-size: 4rem; font-weight: 600;">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <h5 class="card-title">{{ $customer->name }}</h5>
                <p class="card-text">
                    <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }}">
                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                <p class="card-text">
                    <i class="bi bi-envelope"></i> {{ $customer->email }}<br>
                    <i class="bi bi-telephone"></i> {{ $customer->country_code }} {{ $customer->phone_number }}<br>
                    @if($customer->customerProfile->city || $customer->customerProfile->country)
                        <i class="bi bi-geo-alt"></i> {{ $customer->customerProfile->city }}{{ $customer->customerProfile->city && $customer->customerProfile->country ? ', ' : '' }}{{ $customer->customerProfile->country }}
                    @endif
                </p>
                <p class="card-text">
                    <small class="text-muted">Registered on {{ $customer->customerProfile->created_at->format('M d, Y') }}</small>
                </p>
            </div>
        </div>

        <!-- Account Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Account Information</h6>
            </div>
            <div class="card-body">
                <p><strong>Customer ID:</strong> #{{ $customer->id }}</p>
                <p><strong>Role:</strong> {{ ucfirst($customer->role) }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }}">
                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                <p><strong>Profile Verification:</strong> 
                    @if($customer->customerProfile && $customer->customerProfile->is_verified)
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning">Unverified</span>
                    @endif
                </p>
                <p><strong>Created:</strong> {{ $customer->customerProfile->created_at->format('M d, Y g:i A') }}</p>
                <p><strong>Last Updated:</strong> {{ $customer->customerProfile->updated_at->format('M d, Y g:i A') }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8 right-col">
        <!-- Personal Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Personal Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Age:</strong> {{ $customer->customerProfile->age ?? 'Not provided' }}</p>
                        <p><strong>Biological Sex:</strong> {{ $customer->customerProfile->gender ? ucfirst($customer->customerProfile->gender) : 'Not provided' }}</p>
                        <p><strong>Weight:</strong> {{ $customer->customerProfile->weight ? $customer->customerProfile->weight . ' kg' : 'Not provided' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Country:</strong> {{ $customer->customerProfile->country ?? 'Not provided' }}</p>
                        <p><strong>City:</strong> {{ $customer->customerProfile->city ?? 'Not provided' }}</p>
                        @if($customer->customerProfile)
                            <p><strong>Profile Gender:</strong> {{ $customer->customerProfile->gender ?? 'Not provided' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Medical Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Allergies:</strong></h6>
                        <p>
                            @php
                                $allergies = $customer->allergies ?? ($customer->customerProfile->allergies ?? null);
                                if ($allergies) {
                                    $decoded = json_decode($allergies);
                                    if (is_array($decoded)) {
                                        echo implode(', ', $decoded);
                                    } else {
                                        echo $allergies;
                                    }
                                } else {
                                    echo 'None reported';
                                }
                            @endphp
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Chronic Pathologies:</strong></h6>
                        <p>
                            @php
                                $pathologies = $customer->customerProfile->chronic_pathologies ?? null;
                                if ($pathologies) {
                                    $decoded = json_decode($pathologies);
                                    if (is_array($decoded)) {
                                        echo implode(', ', $decoded);
                                    } else {
                                        echo $pathologies;
                                    }
                                } else {
                                    echo 'None reported';
                                }
                            @endphp
                        </p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Chronic Medication:</strong></h6>
                        <p>
                            @php
                                $medications = $customer->customerProfile->chronic_medications ?? null;
                                if ($medications) {
                                    $decoded = json_decode($medications);
                                    if (is_array($decoded)) {
                                        echo implode(', ', $decoded);
                                    } else {
                                        echo $medications;
                                    }
                                } else {
                                    echo 'None reported';
                                }
                            @endphp
                        </p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Additional Medical Information:</strong></h6>
                        <p>{{ $customer->customerProfile->medical_info ?? 'None provided' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Synchronization Status -->
        @if($customer->customerProfile)
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Profile Synchronization</h6>
                <span class="badge bg-success">
                    <i class="bi bi-check-circle"></i> Synced
                </span>
            </div>
            <div class="card-body">
                <p class="text-success">
                    <i class="bi bi-check-circle me-2"></i>
                    Customer profile is properly synchronized with user account.
                </p>
                <small class="text-muted">
                    Profile last updated: {{ $customer->customerProfile->updated_at->format('M d, Y g:i A') }}
                </small>
            </div>
        </div>
        @else
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Profile Synchronization</h6>
                <span class="badge bg-warning">
                    <i class="bi bi-exclamation-triangle"></i> Missing
                </span>
            </div>
            <div class="card-body">
                <p class="text-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Customer profile is missing. This may cause issues with some features.
                </p>
                <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="create_profile" value="1">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Create Profile
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Payment Methods -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Payment Methods</h6>
            </div>
            <div class="card-body">
                @if($customer->paymentMethods && count($customer->paymentMethods) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Last 4 Digits</th>
                                    <th>Added On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->paymentMethods as $method)
                                <tr>
                                    <td>
                                        @if($method->type == 'visa')
                                            <i class="bi bi-credit-card text-primary"></i> Visa
                                        @elseif($method->type == 'mastercard')
                                            <i class="bi bi-credit-card text-warning"></i> Mastercard
                                        @elseif($method->type == 'paypal')
                                            <i class="bi bi-paypal text-info"></i> PayPal
                                        @else
                                            <i class="bi bi-credit-card"></i> {{ ucfirst($method->type) }}
                                        @endif
                                    </td>
                                    <td>{{ $method->name ?? 'N/A' }}</td>
                                    <td>{{ $method->last_four ?? 'N/A' }}</td>
                                    <td>{{ $method->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-credit-card text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">No payment methods added yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
@endsection
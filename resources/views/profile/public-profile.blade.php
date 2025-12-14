@extends('layouts.mainLayout')

@section('title', $userData['name'] . ' - Profile')
@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/public-profile.css') }}">
@endsection

@section('content')
<div class="public-profile-container">
    <div class="profile-content">
        <!-- Left Column - Basic Information -->
        <div class="basic-info-section">
            <div class="profile-card">
                <div class="profile-image">
                    <img src="{{ $userData['profile_photo'] }}" 
                         alt="{{ $userData['name'] }}" 
                         onerror="this.onerror=null;this.src='/assets/icons/default-avatar.svg';">
                </div>
                
                <div class="profile-details">
                    <h1 class="profile-name">{{ $userData['name'] }}</h1>
                    <p class="profile-phone">{{ $userData['country_code'] }}{{ $userData['phone'] }}</p>
                    
                    <button class="contact-btn">
                        <i class="fas fa-envelope"></i>
                        Contact
                    </button>
                </div>
            </div>
            
            <div class="basic-info-card">
                <h2>Basic Information</h2>
                
                <div class="info-row">
                    <span class="info-label">Country</span>
                    <span class="info-value">{{ $userData['country'] ?? 'Not specified' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">City</span>
                    <span class="info-value">{{ $userData['city'] ?? 'Not specified' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Biological Sex</span>
                    <span class="info-value">{{ ucfirst($userData['biological_sex'] ?? 'Not specified') }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Age</span>
                    <span class="info-value">{{ $userData['age'] ?? 'Not specified' }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Weight</span>
                    <span class="info-value">{{ $userData['weight'] ? $userData['weight'] . ' kg' : 'Not specified' }}</span>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Health Information -->
        <div class="health-info-section">
            <h2>Health Information</h2>
            
            <div class="health-card">
                <h3>Chronic Pathologies</h3>
                <div class="health-content">
                    @php
                        $chronicPathologies = [];
                        
                        // Try to get from customer profile first
                        if ($user->customerProfile && $user->customerProfile->chronic_pathologies) {
                            $data = $user->customerProfile->chronic_pathologies;
                            
                            // Try JSON decode first
                            $decoded = json_decode($data);
                            if (is_array($decoded)) {
                                $chronicPathologies = $decoded;
                            } else {
                                // If not JSON, try semicolon-separated string
                                $chronicPathologies = array_map('trim', explode(';', $data));
                            }
                        }
                        
                        // If empty, try to get from user table
                        if (empty($chronicPathologies) && $user->customerProfile && $user->customerProfile->chronic_pathologies) {
                    $data = $user->customerProfile->chronic_pathologies;
                            
                            // Try JSON decode first
                            $decoded = json_decode($data);
                            if (is_array($decoded)) {
                                $chronicPathologies = $decoded;
                            } else {
                                // If not JSON, try semicolon-separated string
                                $chronicPathologies = array_map('trim', explode(';', $data));
                            }
                        }
                        
                        // Filter out empty values
                        $chronicPathologies = array_filter($chronicPathologies, function($item) {
                            return !empty(trim($item));
                        });
                    @endphp
                    
                    @if(count($chronicPathologies) > 0)
                        <p>{{ implode(', ', $chronicPathologies) }}</p>
                    @else
                        <p>None</p>
                    @endif
                </div>
            </div>
            
            <div class="health-card">
                <h3>Chronic Medication</h3>
                <div class="health-content">
                    @php
                        $chronicMedications = [];
                        
                        // Try to get from customer profile first
                        if ($user->customerProfile && $user->customerProfile->chronic_medications) {
                            $data = $user->customerProfile->chronic_medications;
                            
                            // Try JSON decode first
                            $decoded = json_decode($data);
                            if (is_array($decoded)) {
                                $chronicMedications = $decoded;
                            } else {
                                // If not JSON, try semicolon-separated string
                                $chronicMedications = array_map('trim', explode(';', $data));
                            }
                        }
                        
                        // If empty, try to get from user table
                        if (empty($chronicMedications) && $user->customerProfile && $user->customerProfile->chronic_medications) {
                    $data = $user->customerProfile->chronic_medications;
                            
                            // Try JSON decode first
                            $decoded = json_decode($data);
                            if (is_array($decoded)) {
                                $chronicMedications = $decoded;
                            } else {
                                // If not JSON, try semicolon-separated string
                                $chronicMedications = array_map('trim', explode(';', $data));
                            }
                        }
                        
                        // Filter out empty values
                        $chronicMedications = array_filter($chronicMedications, function($item) {
                            return !empty(trim($item));
                        });
                    @endphp
                    
                    @if(count($chronicMedications) > 0)
                        <p>{{ implode(', ', $chronicMedications) }}</p>
                    @else
                        <p>None</p>
                    @endif
                </div>
            </div>
            
            <div class="health-card">
                <h3>Allergies</h3>
                <div class="health-content">
                    @php
                        $allergies = [];
                        
                        // Try to get from customer profile first
                        if ($user->customerProfile && $user->customerProfile->allergies) {
                            $data = $user->customerProfile->allergies;
                            
                            // Try JSON decode first
                            $decoded = json_decode($data);
                            if (is_array($decoded)) {
                                $allergies = $decoded;
                            } else {
                                // If not JSON, try semicolon-separated string
                                $allergies = array_map('trim', explode(';', $data));
                            }
                        }
                        
                        // If empty, try to get from user table
                        if (empty($allergies) && $user->customerProfile && $user->customerProfile->allergies) {
                    $data = $user->customerProfile->allergies;
                            
                            // Try JSON decode first
                            $decoded = json_decode($data);
                            if (is_array($decoded)) {
                                $allergies = $decoded;
                            } else {
                                // If not JSON, try semicolon-separated string
                                $allergies = array_map('trim', explode(';', $data));
                            }
                        }
                        
                        // Filter out empty values
                        $allergies = array_filter($allergies, function($item) {
                            return !empty(trim($item));
                        });
                    @endphp
                    
                    @if(count($allergies) > 0)
                        <p>{{ implode(', ', $allergies) }}</p>
                    @else
                        <p>None</p>
                    @endif
                </div>
            </div>
            
            <div class="health-card">
                <h3>Relevant Medical Information</h3>
                <div class="health-content">
                    @if($user->customerProfile && $user->customerProfile->medical_info)
                        <p>{{ $user->customerProfile->medical_info }}</p>
                    @else
                        <p>No additional medical information provided.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Add any necessary JavaScript here
</script>
@endsection
@extends('layouts.mainLayout')

@section('title', 'My Profile')
@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/user-profile.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/profile-image-modal.css') }}">
@endsection


@section('content')
<div class="container">
    
    <!-- Left Column - Personal Information -->
    <div class="profile-column">
        <div class="profile-photo-container">
            <div class="profile-photo">
                <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('assets/icons/profile.svg') }}" 
                     alt="Profile Photo"
                     onclick="openProfileImageModal('{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('assets/icons/profile.svg') }}')">
                <label for="profile_photo_input" class="edit-photo-btn">
                    <i class="fas fa-camera"></i>
                </label>
                <form action="{{ route('profile.upload-photo') }}" method="POST" enctype="multipart/form-data" id="photo-upload-form" class="signin-form hidden">
                    @csrf
                    <input type="file" name="profile_photo" id="profile_photo_input" class="d-none" accept="image/*">
                </form>
            </div>
            <form action="{{ route('profile.delete-photo') }}" method="POST">
                @csrf
                <button type="submit" class="delete-photo-btn">Delete Photo</button>
            </form>
        </div>

        <div class="personal-info-section">
            <h2>Personal Information</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" id="profile-form" class="user-profile-form">
                @csrf
                <div class="label-input">
                    <label for="fullName">Full Name</label>
                    <div class="field-box">
                        <div class="input-group field-control @error('full_name') error-border @enderror">
                            <input type="text" id="fullName" placeholder="Full Name" name="full_name" value="{{ old('full_name', $user->customerProfile->name ?? $user->name ?? '') }}">
                        </div>

                        @error('full_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="label-input">
                    <label for="country">Country</label>
                    <div class="field-box">
                        <div class="input-group field-control @error('country') error-border @enderror">
                            <input type="text" id="country" placeholder="Country" name="country" value="{{ old('country', $user->customerProfile->country ?? '') }}">
                        </div>

                        @error('country')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="label-input">
                    <label for="city">City Or Village</label>
                    <div class="field-box">
                        <div class="input-group field-control @error('city') error-border @enderror">
                            <input type="text" id="city" placeholder="City Or Village" name="city" value="{{ old('city', $user->customerProfile->city ?? '') }}">
                        </div>

                        @error('city')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="label-input">
                    <label>Biological Sex</label>
                    <div class="input-group field-control gender-group">
                        <input type="hidden" name="biological_sex" id="gender" value="{{ strtolower(old('biological_sex', $user->customerProfile->gender ?? 'male')) }}">

                        <button type="button" class="btn btn-primary gender {{ strtolower($user->customerProfile->gender ?? 'male') == 'female' ? 'active' : '' }}" data-value="female">Female</button>

                        <button type="button" class="btn btn-primary gender {{ strtolower($user->customerProfile->gender ?? 'male') == 'male' ? 'active' : '' }}" data-value="male">Male</button>
                    </div>
                    @error('biological_sex')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="label-input">
                    <label for="age">Your Age</label>
                    <div class="field-box">
                        <div class="input-group field-control @error('age') error-border @enderror">
                            <input type="number" id="age" placeholder="Age" name="age" value="{{ old('age', $user->customerProfile->age ?? '') }}">
                        </div>

                        @error('age')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                
                <div class="label-input">
                    <label for="weight">Your Weight</label>
                    <div class="field-box">
                        <div class="input-group field-control @error('weight') error-border @enderror">
                            <input type="number" id="weight" placeholder="Weight" name="weight" value="{{ old('weight', $user->customerProfile->weight ?? '') }}" step="0.1" class="form-control">
                            <span>Kg</span>
                        </div>

                        @error('weight')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="label-input">
                    <label>Chronic Pathologies</label>
                    <div class="chronic-pathologies-container">
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
                            @foreach($chronicPathologies as $index => $pathology)
                                <div class="input-group field-control">
                                    <input type="text" name="chronic_pathologies[]" placeholder="E.G. Diabetes" value="{{ $pathology }}">
                                    @if($index > 0)
                                        <button type="button" class="remove-field-btn">✕</button>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="input-group field-control">
                                <input type="text" name="chronic_pathologies[]" placeholder="E.G. Diabetes" value="">
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn dotted-btn">+ Add More</button>
                </div>

                <div class="label-input">
                    <label>Allergies</label>
                    <div class="allergies-container">
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
                            @foreach($allergies as $index => $allergy)
                                <div class="input-group field-control">
                                    <input type="text" name="allergies[]" placeholder="E.G. Peanuts, Shellfish" value="{{ $allergy }}">
                                    @if($index > 0)
                                        <button type="button" class="remove-field-btn">✕</button>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="input-group field-control">
                                <input type="text" name="allergies[]" placeholder="E.G. Peanuts, Shellfish" value="">
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn dotted-btn">+ Add More</button>
                </div>

                <div class="label-input">
                    <label>Chronic Medication</label>
                    <div class="chronic-medications-container">
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
                            @foreach($chronicMedications as $index => $medication)
                                <div class="input-group field-control">
                                    <input type="text" name="chronic_medication[]" placeholder="Chronic Medication" value="{{ $medication }}">
                                    @if($index > 0)
                                        <button type="button" class="remove-field-btn">✕</button>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="input-group field-control">
                                <input type="text" name="chronic_medication[]" placeholder="Chronic Medication" value="">
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn dotted-btn">+ Add More</button>
                </div>

                <div class="label-input">
                    <label>Relevant Medical Information</label>
                    <textarea name="medical_info" placeholder="Additional Information About Your Health" class="input-group medical-info-input">{{ old('medical_info', $user->customerProfile->medical_info ?? '') }}</textarea>
                </div>
                

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Right Column - Payment Methods -->
    <div class="payment-column">

        <div class="card-section">

            <h2>My Payment Methods</h2>
    
            <div class="saved-cards">
                @forelse($paymentMethods as $paymentMethod)
                    <div class="card-item">
                        <div class="card-info">
                            <div class="card-logo {{ $paymentMethod->card_type }}">
                                @if($paymentMethod->card_type == 'visa')
                                    <img src="{{ asset('assets/images/card1.png') }}" alt="Visa">
                                @elseif($paymentMethod->card_type == 'mastercard')
                                    <img src="{{ asset('assets/images/card4.png') }}" alt="Mastercard">
                                @else
                                    <img src="{{ asset('assets/images/card1.png') }}" alt="Card">
                                @endif
                            </div>
                            <div class="card-details">
                                <div class="card-owner">{{ $paymentMethod->card_holder }}</div>
                                <div class="card-number">{{ $paymentMethod->masked_number }}</div>
                            </div>
                        </div>
                        <form action="{{ route('payment-methods.destroy', $paymentMethod->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-card-btn">Delete</button>
                        </form>
                    </div>
                @empty
                    <div class="no-cards-message">
                        <p>No payment methods added yet.</p>
                    </div>
                @endforelse
            </div>
    
            <div class="add-card-section">
                <h3>Add New Card</h3>
    
                <div class="card-form">
                    <form action="{{ route('payment-methods.store') }}" method="POST" id="payment-form">
                        @csrf
                        <div class="form-row">

                            <div class="label-input">
                                <label for="cardNumber">Enter Card Number</label>
                                <div class="field-box">
                                    <div class="input-group field-control @error('card_number') error-border @enderror">
                                        <input type="text" id="cardNumber" name="card_number" placeholder="Card Number" maxlength="19" autocomplete="cc-number" value="{{ old('card_number') }}">
                                    </div>
            
                                    @error('card_number')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="label-input">
                                <label for="cardHolder">Card Holder Name</label>
                                <div class="field-box">
                                    <div class="input-group field-control @error('card_holder') error-border @enderror">
                                        <input type="text" id="cardHolder" name="card_holder" placeholder="Card Holder Name" autocomplete="cc-name" value="{{ old('card_holder') }}">
                                    </div>
            
                                    @error('card_holder')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <div class="form-row">

                            <div class="label-input expire-date">
                                <label>Expire Date</label>
                                <div class="expire-date-row">
                                    <div class="field-box">
                                        <div class="input-group field-control @error('expiry_month') error-border @enderror @error('expiry_date') error-border @enderror">
                                            <input type="text" name="expiry_month" placeholder="MM" maxlength="2" value="{{ old('expiry_month') }}">
                                        </div>
                
                                        @error('expiry_month')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="field-box">
                                        
                                        <div class="input-group field-control @error('expiry_year') error-border @enderror @error('expiry_date') error-border @enderror">
                                            <input type="text" name="expiry_year" placeholder="YY" maxlength="2" value="{{ old('expiry_year') }}">
                                        </div>
                
                                        @error('expiry_year')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                                @error('expiry_date')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="label-input">
                                <label>CVV Code</label>
                                <div class="field-box">
                                    <div class="input-group field-control @error('cvv') error-border @enderror">
                                        <input type="text" name="cvv" placeholder="CVV" maxlength="4" autocomplete="cc-csc" value="{{ old('cvv') }}">
                                    </div>
            
                                    @error('cvv')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <div class="btn-row">

                            <button class="btn btn-primary">Add New Payment Method</button>
    
                            <div class="terms-text">
                                By adding a card, you agree to the <br>
                                <a href="#" class="terms-link">Terms & Conditions</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the profile image modal component -->
@include('partials.profile_image_modal')
@endsection

@section('script')
<script src="{{ asset('js/partials/dynamic-toggle.js') }}"></script>
<script src="{{ asset('js/components/profile-image-modal.js') }}"></script>
<script src="{{ asset('js/profile/user-profile.js') }}"></script>
@endsection
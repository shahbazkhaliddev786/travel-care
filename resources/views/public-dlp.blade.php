@extends('layouts.mainLayout')
@section('title', $doctor->name . ' - Public Profile')
@section('css')
<link rel="stylesheet" href="{{ asset('css/public-dlp.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/profile-image-modal.css') }}">
@endsection

@section('content')

<!-- Main Content -->
<main class="main-content">
    <!-- Left Column -->
    <div class="profile-left">
        <div class="profile-card">
            <img src="{{ $doctor->profile_image ? asset('storage/' . $doctor->profile_image) . '?v=' . time() : asset('assets/icons/profile.svg') }}" alt="{{ $doctor->name }}" class="doctor-img" onclick="openProfileImageModal('{{ $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : asset('assets/icons/profile.svg') }}', {{ json_encode($doctor->gallery_images ? array_map(function($img) { return asset('storage/' . $img) . '?v=' . time(); }, $doctor->gallery_images) : []) }})">
            <h1>{{ $doctor->name }}</h1>
            <p class="specialty">{{ $doctor->speciality ?? 'Medical Professional' }}</p>
                            <a href="{{ route('appointment', $doctor->id) }}">
                    <button class="book-appointment">Book An Appointment</button>
            </a>
        </div>

        <!-- About Section -->
        <div class="about-section">
            <h2>About the doctor</h2>
            <div class="stats">
                <div class="stat">
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <span>{{ $doctor->rating ?? '4.7' }}</span>
                    </div>
                    <p>({{ $doctor->reviews_count ?? '0' }} Reviews)</p>
                </div>
                <div class="stat">
                    <span class="number">{{ $doctor->patients_count ?? '0' }}+</span>
                    <p>Patients</p>
                </div>
                <div class="stat">
                    <span class="number">{{ $doctor->years_of_experience ?? '0' }} Years</span>
                    <p>Experience</p>
                </div>
            </div>

            <div class="info-list">
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $doctor->city ?? 'Location not specified' }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-hospital"></i>
                    <span>{{ $doctor->working_location ?? 'Workplace not specified' }}</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span>{{ $doctor->education ?? 'Education not specified' }}</span>
                </div>
                @if($doctor->publication)
                <div class="info-item">
                    <i class="fas fa-book-medical"></i>
                    <span>{{ $doctor->publication }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Working Time Section -->
        <div class="working-time">
            <h2>Working time</h2>
            <p class="hours">
                <i class="far fa-clock"></i> 
                {{ \Carbon\Carbon::parse($doctor->working_hours_from)->format('g:i A') }} — 
                {{ \Carbon\Carbon::parse($doctor->working_hours_to)->format('g:i A') }}
            </p>
            <div class="weekdays">
                @php
                $days = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];
                $fullDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $workingDays = $doctor->working_days ?? [];
                @endphp
                
                @foreach($days as $index => $day)
                    <span class="{{ in_array($fullDays[$index], $workingDays) ? 'active' : '' }}">{{ $day }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="profile-right">
        <div class="tabs">
            <button class="tab active" data-tab="services">Services</button>
            <button class="tab" data-tab="reviews">Reviews</button>
        </div>

        <div class="tab-container">
            
            <div class="tab-content">
                <div class="tab-pane active" id="services">
                    <div class="services-section">
                        <div class="services-list">
                            @if($doctor->services && $doctor->services->count() > 0)
                                @foreach($doctor->services as $service)
                                <div class="service-item">
                                    <div class="service-info">
                                        <h3>{{ $service->name }}</h3>
                                        <p>{{ $service->description }}</p>
                                    </div>
                                    <button class="price-btn">${{ $service->price }}</button>
                                </div>
                                @endforeach
                            @else
                                @if($doctor->messaging_fee)
                                <div class="service-item">
                                    <div class="service-info">
                                        <h3>Messaging Consultation</h3>
                                        <p>Message consultation with the doctor</p>
                                    </div>
                                    <button class="price-btn">${{ $doctor->messaging_fee }}</button>
                                </div>
                                @endif
                                
                                @if($doctor->video_call_fee)
                                <div class="service-item">
                                    <div class="service-info">
                                        <h3>Video Call</h3>
                                        <p>Video consultation with the doctor</p>
                                    </div>
                                    <button class="price-btn">${{ $doctor->video_call_fee }}</button>
                                </div>
                                @endif
                                
                                @if($doctor->house_visit_fee)
                                <div class="service-item">
                                    <div class="service-info">
                                        <h3>House Visit</h3>
                                        <p>Home visit with consultation</p>
                                    </div>
                                    <button class="price-btn">${{ $doctor->house_visit_fee }}</button>
                                </div>
                                @endif
                                
                                @if($doctor->voice_call_fee)
                                <div class="service-item">
                                    <div class="service-info">
                                        <h3>Voice Call</h3>
                                        <p>Voice call consultation with the doctor</p>
                                    </div>
                                    <button class="price-btn">${{ $doctor->voice_call_fee }}</button>
                                </div>
                                @endif
                                @if(!$doctor->messaging_fee && !$doctor->video_call_fee && !$doctor->house_visit_fee && !$doctor->voice_call_fee)
                                    <div style="text-align:center; padding: 24px; color: var(--grey);">No service available</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane" id="reviews">
                    <div class="reviews-section">
                        <div class="reviews-header">
                            <h2>{{ $doctor->reviews_count ?? '0' }} Reviews</h2>
                        </div>
                        <div class="reviews-list">
                            @if(isset($reviews) && $reviews->count() > 0)
                                @foreach($reviews as $review)
                                <div class="review-item">
                                    <div class="reviewer-info">
                                        <img src="{{ $review->user->profile_image ? asset('storage/' . $review->user->profile_image) : asset('assets/icons/profile.svg') }}" alt="{{ $review->user->name }}" class="reviewer-img">
                                        <div class="reviewer-details">
                                            <div class="review-rating">
                                                <h3>{{ $review->user->name }}</h3>
                                                <div class="stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <p>{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div style="text-align:center; padding: 24px; color: var(--grey);">No Review available</div>
                            @endif
                        </div>
                        
                        @if(isset($reviews) && $reviews->hasPages())
                        <div class="pagination">
                            @if($reviews->onFirstPage())
                                <button class="prev-page" disabled><i class="fas fa-chevron-left"></i></button>
                            @else
                                <a href="{{ $reviews->previousPageUrl() }}#reviews" class="prev-page"><i class="fas fa-chevron-left"></i></a>
                            @endif
                            <div class="page-numbers">
                                @php $maxVisible = 15; $end = min($reviews->lastPage(), $maxVisible); @endphp
                                @foreach($reviews->getUrlRange(1, $end) as $page => $url)
                                    @if($page == $reviews->currentPage())
                                        <span class="active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}#reviews"><span>{{ $page }}</span></a>
                                    @endif
                                @endforeach
                            </div>
                            @if($reviews->hasMorePages())
                                <a href="{{ $reviews->nextPageUrl() }}#reviews" class="next-page"><i class="fas fa-chevron-right"></i></a>
                            @else
                                <button class="next-page" disabled><i class="fas fa-chevron-right"></i></button>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="service-popup-backdrop" id="servicePopup">
    <div class="service-popup">
        <h3 class="service-popup-title">Do you want to purchase this service?</h3>
        <p class="service-popup-desc"><span id="popupServiceName"></span> <span id="popupServicePrice"></span></p>
        <div class="service-popup-actions">
            <button class="service-popup-btn confirm" id="servicePopupConfirm">Yes, Go To Payment</button>
            <button class="service-popup-btn cancel" id="servicePopupCancel">No, go back</button>
        </div>
    </div>
</div>

<!-- Include the profile image modal component -->
@include('partials.profile_image_modal')

@endsection

@section('script')
<script src="{{ asset('js/public-dlp.js') }}"></script>
<script src="{{ asset('js/components/profile-image-modal.js') }}"></script>
@endsection
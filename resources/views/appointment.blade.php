@extends('layouts.mainLayout')
@section('title', 'Book Appointment')
@section('css')
<link rel="stylesheet" href="{{ asset('css/appointment.css') }}">
<link rel="stylesheet" href="{{ asset('css/appointment-responsive.css') }}">
@endsection

@section('content')

<!-- Main Content -->
<main class="main-content">
        
    <!-- Left Column -->
    <div class="profile-left">
        <div class="profile-card">
            <img src="{{ $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : '/assets/icons/profile.svg' }}" 
                 alt="{{ $doctor->name }}" class="doctor-img">
            <h1>{{ $doctor->name }}</h1>
            @if($doctor->specialization)
                <p class="specialty">{{ $doctor->specialization }}</p>
            @else
                <p class="specialty missing-data">
                    <i class="fas fa-question-circle"></i>
                    Specialty not specified
                </p>
            @endif
        </div>

        <!-- About Section -->
        <div class="about-section">
            <h2>About the doctor</h2>
            <div class="stats">
                <div class="stat">
                    @if($doctor->rating > 0)
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span>{{ $doctor->rating }}</span>
                        </div>
                        <p>({{ $doctor->reviews_count }} Reviews)</p>
                    @else
                        <div class="rating missing-data">
                            <i class="fas fa-question-circle"></i>
                            <span>No rating</span>
                        </div>
                        <p>(No reviews yet)</p>
                    @endif
                </div>
                <div class="stat">
                    @if($doctor->patients_count > 0)
                        <span class="number">{{ $doctor->patients_count }}+</span>
                        <p>Patients</p>
                    @else
                        <span class="number missing-data">
                            <i class="fas fa-question-circle"></i>
                            Not available
                        </span>
                        <p>Patients</p>
                    @endif
                </div>
                <div class="stat">
                    @if($doctor->years_of_experience)
                        <span class="number">{{ $doctor->years_of_experience }} Years</span>
                        <p>Experience</p>
                    @else
                        <span class="number missing-data">
                            <i class="fas fa-question-circle"></i>
                            Not specified
                        </span>
                        <p>Experience</p>
                    @endif
                </div>
            </div>

            <div class="info-list">
                @if($doctor->city)
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $doctor->city }}{{ $doctor->address ? ', ' . $doctor->address : '' }}</span>
                    </div>
                @else
                    <div class="info-item missing-data">
                        <i class="fas fa-question-circle"></i>
                        <span>Location not provided</span>
                    </div>
                @endif
                
                @if($doctor->working_location)
                    <div class="info-item">
                        <i class="fas fa-hospital"></i>
                        <span>{{ $doctor->working_location }}</span>
                    </div>
                @else
                    <div class="info-item missing-data">
                        <i class="fas fa-question-circle"></i>
                        <span>Workplace not specified</span>
                    </div>
                @endif
                
                @if($doctor->description)
                    <div class="info-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>{{ $doctor->description }}</span>
                    </div>
                @else
                    <div class="info-item missing-data">
                        <i class="fas fa-question-circle"></i>
                        <span>Education/Description not provided</span>
                    </div>
                @endif
                
                @if($doctor->specialization)
                    <div class="info-item">
                        <i class="fas fa-book-medical"></i>
                        <span>Specializes in {{ $doctor->specialization }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Working Time Section -->
        <div class="working-time">
            <h2>Working time</h2>
            @if($doctor->working_hours_from && $doctor->working_hours_to)
                <p class="hours">
                    <i class="far fa-clock"></i> 
                    @php
                        try {
                            $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $doctor->working_hours_from)->format('g:i A');
                        } catch (\Exception $e) {
                            $startTime = \Carbon\Carbon::createFromFormat('H:i', $doctor->working_hours_from)->format('g:i A');
                        }
                        try {
                            $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $doctor->working_hours_to)->format('g:i A');
                        } catch (\Exception $e) {
                            $endTime = \Carbon\Carbon::createFromFormat('H:i', $doctor->working_hours_to)->format('g:i A');
                        }
                    @endphp
                    {{ $startTime }} — {{ $endTime }}
                </p>
            @else
                <p class="hours missing-data">
                    <i class="fas fa-question-circle"></i>
                    Working hours not specified
                </p>
            @endif
            
            <div class="weekdays">
                @php
                    $workingDays = $doctor->working_days ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                    $dayAbbreviations = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];
                    $fullDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                @endphp
                
                @foreach($dayAbbreviations as $index => $dayAbbr)
                    <span class="{{ in_array($fullDays[$index], $workingDays) ? 'active' : '' }}">{{ $dayAbbr }}</span>
                @endforeach
            </div>
        </div>
    </div>


    <!-- Appointment Section -->
    <div class="appointment-section">
        <h2>Book an Appointment</h2>
        
        <!-- Date Selection -->
        <div class="date-selection">
            <button class="date-nav prev"><i class="fas fa-chevron-left"></i></button>
            <div class="dates">
                @foreach($availableDates as $index => $dateInfo)
                <div class="date-item {{ $index === 1 ? 'active' : '' }}">
                    <span class="day">{{ $dateInfo['day'] }}</span>
                    <span class="date">{{ $dateInfo['available'] }} Available</span>
                </div>
                @endforeach
            </div>
            <button class="date-nav next"><i class="fas fa-chevron-right"></i></button>
        </div>

        <!-- Time Slots -->
        <div class="time-slots">
            @if(count($timeSlots) > 0)
                @php
                    $startTime = $doctor->working_hours_from ?? '08:00:00';
                    $endTime = $doctor->working_hours_to ?? '19:00:00';
                    try {
                        $start = \Carbon\Carbon::createFromFormat('H:i:s', $startTime);
                    } catch (\Exception $e) {
                        $start = \Carbon\Carbon::createFromFormat('H:i', $startTime);
                    }
                    try {
                        $end = \Carbon\Carbon::createFromFormat('H:i:s', $endTime);
                    } catch (\Exception $e) {
                        $end = \Carbon\Carbon::createFromFormat('H:i', $endTime);
                    }
                @endphp
                <h3>Available Times, {{ $start->format('gA') }} - {{ $end->format('gA') }} <i class="fas fa-chevron-right"></i></h3>
                <div class="time-grid">
                    @foreach($timeSlots as $index => $slot)
                        <button class="time-slot {{ $index === 4 ? 'active' : '' }}" data-time="{{ $slot['value'] }}">
                            {{ $slot['time'] }}
                        </button>
                    @endforeach
                </div>
            @else
                <h3>No Available Times</h3>
                <div class="missing-data">
                    <i class="fas fa-question-circle"></i>
                    <span>Doctor's working hours not set</span>
                </div>
            @endif
        </div>

        <!-- Fees Information -->
        <div class="fees-section">
            <h3>Fees Information</h3>
            <div class="fee-options">
                <div class="fee-item">
                    <div class="fee-info">
                        <i class="fas fa-phone"></i>
                        <div class="fee-details">
                            <h4>Voice Call</h4>
                            <p>The doctor will call you</p>
                        </div>
                    </div>
                    @if($doctor->voice_call_fee)
                        <span class="fee-amount">${{ $doctor->voice_call_fee }}</span>
                    @else
                        <span class="fee-amount missing-data">
                            <i class="fas fa-question-circle"></i>
                            Not available
                        </span>
                    @endif
                </div>

                <div class="fee-item">
                    <div class="fee-info">
                        <i class="fas fa-comment"></i>
                        <div class="fee-details">
                            <h4>Messaging</h4>
                            <p>You can messaging with the doctor</p>
                        </div>
                    </div>
                    @if($doctor->messaging_fee)
                        <span class="fee-amount">${{ $doctor->messaging_fee }}</span>
                    @else
                        <span class="fee-amount missing-data">
                            <i class="fas fa-question-circle"></i>
                            Not available
                        </span>
                    @endif
                </div>

                <div class="fee-item">
                    <div class="fee-info">
                        <i class="fas fa-video"></i>
                        <div class="fee-details">
                            <h4>Video Call</h4>
                            <p>You can make video call with the doctor</p>
                        </div>
                    </div>
                    @if($doctor->video_call_fee)
                        <span class="fee-amount">${{ $doctor->video_call_fee }}</span>
                    @else
                        <span class="fee-amount missing-data">
                            <i class="fas fa-question-circle"></i>
                            Not available
                        </span>
                    @endif
                </div>

                <div class="fee-item active">
                    <div class="fee-info">
                        <i class="fas fa-home"></i>
                        <div class="fee-details">
                            <h4>House Visit</h4>
                            <p>Doctor comes to your location</p>
                        </div>
                    </div>
                    @if($doctor->house_visit_fee)
                        <span class="fee-amount">${{ $doctor->house_visit_fee }}</span>
                    @else
                        <span class="fee-amount missing-data">
                            <i class="fas fa-question-circle"></i>
                            Not available
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Address Input -->
        <div class="address-section">
            <h3>Add Your Address</h3>
            <input type="text" placeholder="Enter Your Visit Address" class="address-input">
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn-cancel">Cancel</button>
            <button class="btn-payment">Go To Payment</button>
        </div>
    </div>
</main>

@endsection

@section('script')

<script src="{{ asset('js/appointment.js') }}"></script>

@endsection
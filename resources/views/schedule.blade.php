@extends('layouts.mainLayout')

@section('title', 'My Schedule - TravelCare')

@section('css')
<link rel="stylesheet" href="{{ asset('css/schedule.css') }}">
@endsection

@section('content')
<div class="schedule-container">
    <div class="schedule-content">
        <!-- Calendar Section -->
        <div class="calendar-section">
            <div class="calendar-header">
                <button class="nav-button prev-month" id="prevMonth">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <h2 class="calendar-title" id="calendarTitle">August, 2023</h2>
                <button class="nav-button next-month" id="nextMonth">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
            
            <div class="calendar-wrapper">
                <div class="calendar-days-header">
                    <div class="day-header">MON</div>
                    <div class="day-header">TUE</div>
                    <div class="day-header">WED</div>
                    <div class="day-header">THU</div>
                    <div class="day-header">FRI</div>
                    <div class="day-header">SAT</div>
                    <div class="day-header">SUN</div>
                </div>
                
                <div class="calendar-days" id="calendarDays">
                    <!-- Calendar days will be populated by JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- Appointment Schedule Section -->
        <div class="appointment-section">
            <h2 class="appointment-title">My Appointment Schedule</h2>
            
            <div class="appointments-list">
                @if($groupedAppointments->isEmpty())
                    <div class="no-appointments">
                        <p>No upcoming appointments scheduled.</p>
                        @if($user->role !== 'doctor')
                            <a href="{{ route('home') }}" class="btn btn-primary">Book an Appointment</a>
                        @endif
                    </div>
                @else
                    @foreach($groupedAppointments as $dayGroup)
                        <div class="appointment-date-group">
                            <h3 class="appointment-date">{{ $dayGroup['formatted_date'] }}</h3>
                            
                            @foreach($dayGroup['appointments'] as $appointment)
                                @php
                                    $serviceClass = match(strtolower(str_replace(' ', '-', $appointment->service_type))) {
                                        'messaging' => 'messaging',
                                        'voice-call', 'video-call' => 'voice-call',
                                        'house-visit' => 'house-visit',
                                        default => 'voice-call'
                                    };
                                    
                                    // For doctor view, show patient name; for customer view, show doctor name
                                    $displayName = ($user->role === 'doctor') 
                                        ? ($appointment->user->name ?? 'Unknown Patient')
                                        : $appointment->doctor_name;
                                        
                                    $patientAge = ($user->role === 'doctor') 
                                        ? ($appointment->user->age ?? 'N/A')
                                        : 'N/A';
                                        
                                    $appointmentTime = $appointment->appointment_time 
                                        ? \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') . ', ' . $dayGroup['formatted_date']
                                        : $dayGroup['formatted_date'];
                                @endphp
                                
                                <div class="appointment-item" 
                                     data-transaction-id="{{ $appointment->transaction_id }}"
                                     data-patient="{{ $displayName }}" 
                                     data-age="{{ $patientAge }}" 
                                     data-service="{{ $appointment->service_type }}" 
                                     data-time="{{ $appointmentTime }}" 
                                     data-location="{{ $appointment->location ?? 'Remote Consultation' }}" 
                                     data-fee="{{ $appointment->formatted_amount }}">
                                    <div class="appointment-info">
                                        <h4 class="patient-name">{{ $displayName }}</h4>
                                        <span class="appointment-type {{ $serviceClass }}">{{ $appointment->service_type }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Appointment Info Modal -->
<div id="appointmentModal" class="appointment-modal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Appointment Info</h3>
            <button class="modal-close" id="closeModal">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="appointment-details">
                <div class="detail-row">
                    <span class="detail-label">{{ $user->role === 'doctor' ? 'Patient' : 'Doctor' }}:</span>
                    <span class="detail-value" id="modalPatient"></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Age:</span>
                    <span class="detail-value" id="modalAge"></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Service:</span>
                    <span class="detail-value" id="modalService"></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Appointment Time:</span>
                    <span class="detail-value" id="modalTime"></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Location:</span>
                    <span class="detail-value" id="modalLocation"></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Fee Paid:</span>
                    <span class="detail-value" id="modalFee"></span>
                </div>
            </div>
        </div>
        
        <div class="modal-footer">
            @if($user->role === 'doctor')
                <button class="modal-btn primary-btn" id="startVideoCall">
                    Start A Video Call
                </button>
                <button class="modal-btn secondary-btn" id="contactPatient">
                    Contact The Patient
                </button>
            @else
                <button class="modal-btn primary-btn" id="startVideoCall">
                    Contact Doctor
                </button>
                <button class="modal-btn secondary-btn" id="contactDoctor">
                    Write A Review
                </button>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Pass data from PHP to JavaScript
    window.calendarData = @json($calendarData);
    window.userRole = @json($user->role);
</script>
<script src="{{ asset('js/schedule.js') }}"></script>
@endsection 
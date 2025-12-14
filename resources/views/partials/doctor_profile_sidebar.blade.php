<div class="profile-left">
    <div class="profile-section">
        <!-- Profile Image -->
        <div class="profile-image-container">
            <img src="{{ $doctor->profile_image ? asset('storage/' . $doctor->profile_image) . '?v=' . time() : asset('assets/icons/profile.svg') }}" alt="{{ $doctor->name }}" class="profile-image" onclick="openProfileImageModal('{{ $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : asset('assets/icons/profile.svg') }}', {{ json_encode($doctor->gallery_images ? array_map(function($img) { return asset('storage/' . $img) . '?v=' . time(); }, $doctor->gallery_images) : []) }})">
            <div class="edit-icon">
                <i class="fas fa-pen"></i>
            </div>
            <h2 class="doctor-name">{{ $doctor->name }}</h2>
            <a href="{{ route('public-profile') }}" class="view-profile">View Public Profile</a>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <div class="payment-info-left">
                <div class="payment-icon">
                    <i class="fas fa-university"></i>
                </div>
                <span>Payment Info</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </div>
    </div>

    <!-- Appointment Fees -->
    <div class="appointment-fees">
        <h3 class="section-title">Appointment Fees</h3>
        
        <form action="{{ route('profile.update-fees') }}" method="POST" id="fees-form" class="fee-form">
            @csrf
            @method('PUT')
            
            <!-- Messaging Fee -->
            <div class="fee-item">
                <div class="fee-type">
                    <div class="fee-icon">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <span>Messaging</span>
                </div>
                <div class="fee-input">
                    <input type="text" name="messaging_fee" placeholder="Set the price" value="{{ $doctor->messaging_fee ?? '' }}">
                    <span class="currency">$</span>
                </div>
            </div>

            <!-- Video Call Fee -->
            <div class="fee-item">
                <div class="fee-type">
                    <div class="fee-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <span>Video Call</span>
                </div>
                <div class="fee-input">
                    <input type="text" name="video_call_fee" value="{{ $doctor->video_call_fee ?? '' }}" placeholder="Set the price">
                    <span class="currency">$</span>
                </div>
            </div>

            <!-- House Visit Fee -->
            <div class="fee-item">
                <div class="fee-type">
                    <div class="fee-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <span>House Visit</span>
                </div>
                <div class="fee-input">
                    <input type="text" name="house_visit_fee" value="{{ $doctor->house_visit_fee ?? '' }}" placeholder="Set the price">
                    <span class="currency">$</span>
                </div>
            </div>

            <!-- Voice Call Fee -->
            <div class="fee-item">
                <div class="fee-type">
                    <div class="fee-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <span>Voice Call Price</span>
                </div>
                <div class="fee-input">
                    <input type="text" name="voice_call_fee" value="{{ $doctor->voice_call_fee ?? '' }}" placeholder="Set the price">
                    <span class="currency">$</span>
                </div>
            </div>
        </form>
    </div>

    <!-- Working Time -->
    <div class="working-time">
        
        <form action="{{ route('profile.update-working-hours') }}" method="POST" id="working-hours-form">
            @csrf
            @method('PUT')
            
            <div class="time-selector">
                <h3 class="section-title">Working time</h3>
                <div class="time-selector-boxes">
                    
                    <div class="selector-box">
                        <div class="time-label">From:</div>
                        <div class="time-picker">
                            <i class="fas fa-chevron-left time-arrow" data-target="working_hours_from"></i>
                            <span id="working_hours_from_display">{{ \Carbon\Carbon::parse($doctor->working_hours_from ?? '01:00:00')->format('g:i A') }}</span>
                            <input type="hidden" name="working_hours_from" id="working_hours_from" value="{{ $doctor->working_hours_from ?? '01:00:00' }}">
                            <i class="fas fa-chevron-right time-arrow" data-target="working_hours_from"></i>
                        </div>
                    </div>
                    <div class="selector-box">
                        <div class="time-label">To:</div>
                        <div class="time-picker">
                            <i class="fas fa-chevron-left time-arrow" data-target="working_hours_to"></i>
                            <span id="working_hours_to_display">{{ \Carbon\Carbon::parse($doctor->working_hours_to ?? '12:00:00')->format('g:i A') }}</span>
                            <input type="hidden" name="working_hours_to" id="working_hours_to" value="{{ $doctor->working_hours_to ?? '12:00:00' }}">
                            <i class="fas fa-chevron-right time-arrow" data-target="working_hours_to"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Working Days -->
            <div class="working-days">
                <h4 class="days-title">Mark Working Days</h4>
                
                <div class="check-box-selection">
                    @php
                    $workingDays = $doctor->working_days ?? [];
                    $allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    @endphp
                    
                    @foreach($allDays as $day)
                        <div class="day-checkbox">
                            <div class="custom-checkbox {{ in_array($day, $workingDays) ? 'checked' : '' }}" data-day="{{ $day }}">
                                @if(in_array($day, $workingDays))
                                    <i class="fas fa-check"></i>
                                @endif
                            </div>
                            <span class="day-label">{{ $day }}</span>
                            <input type="hidden" name="working_days[]" value="{{ $day }}" {{ !in_array($day, $workingDays) ? 'disabled' : '' }}>
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </div>
</div>
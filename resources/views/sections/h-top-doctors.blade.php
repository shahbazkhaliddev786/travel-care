<!-- Top Doctors Section -->
<section class="top-doctors">
    <div class="section-header">
        <h2 class="section-title">Top Doctors</h2>
        <a href="{{ route('category') }}" class="view-all">View All</a>
    </div>
    <div class="top-doctors-slider">
        
        <button class="slider-arrow prev">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <div class="doctors-grid">
            @foreach($topDoctors as $doctor)
            <div class="doctor-card">
                <a href="{{ route('public-profile.show', $doctor->id) }}">
                    <div class="doctor-img">
                        <img src="{{ $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : '/assets/icons/profile.svg' }}" 
                             alt="{{ $doctor->name }}">
                    @if($doctor->rating > 0)
                        <div class="rating">★ {{ $doctor->rating }}</div>
                    @else
                        <div class="rating missing-data">
                            <i class="fas fa-question-circle"></i>
                            <span>No rating</span>
                        </div>
                    @endif
                </div>
                </a>
                <div class="doctor-info">
                    <h3><a href="{{ route('public-profile.show', $doctor->id) }}">{{ $doctor->name }}</a></h3>
                    <p>
                        @if($doctor->specialization)
                            {{ $doctor->specialization }}
                        @else
                            <span class="missing-data">
                                <i class="fas fa-question-circle"></i>
                                Specialty not specified
                            </span>
                        @endif
                        •
                        @if($doctor->city)
                            {{ $doctor->city }}
                        @else
                            <span class="missing-data">
                                <i class="fas fa-question-circle"></i>
                                Location not provided
                            </span>
                        @endif
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        
        <button class="slider-arrow next">
            <i class="fas fa-chevron-right"></i>
        </button>

    </div>
</section>
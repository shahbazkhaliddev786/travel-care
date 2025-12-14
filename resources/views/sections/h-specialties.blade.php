<section class="specialties">
    <div class="specialties-slider">
        <button class="slider-arrow prev">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div class="specialties-container">
            @foreach ($specialties as $specialty)
                <a href="{{ route('category.specialty', ['specialty' => str()->slug($specialty['name'])]) }}" class="specialty-card">
                    <div class="specialty-icon">
                        <img src="{{ asset($specialty['icon']) }}" alt="{{ $specialty['name'] }}">
                    </div>
                    <span class="specialty-name">{{ $specialty['name'] }}</span>
                    <span class="specialty-count">{{ $specialty['count'] }} doctors</span>
                </a>
            @endforeach
        </div>
        <button class="slider-arrow next">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <a href="{{ route('category') }}" class="btn-appointment">Request A Basic Appointment</a>
</section>
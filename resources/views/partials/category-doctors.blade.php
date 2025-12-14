<div class="doctors-list">
    @forelse($doctors as $doctor)
    <div class="c-doctor-card">
        <a href="{{ route('public-profile.show', $doctor->id) }}">
            <img src="{{ $doctor->profile_image ? asset('storage/' . $doctor->profile_image) : '/assets/icons/profile.svg' }}" 
                 alt="{{ $doctor->name }}" class="c-doctor-img">
        </a>
        <div class="c-doctor-info">
            <div class="c-doctor-header">
                <h3><a href="{{ route('public-profile.show', $doctor->id) }}">{{ $doctor->name }}</a></h3>
                @if($doctor->consultation_fee)
                    <div class="price">${{ $doctor->consultation_fee }}</div>
                @else
                    <div class="price missing-data">
                        <i class="fas fa-question-circle"></i>
                        <span>Fee not set</span>
                    </div>
                @endif
            </div>
            <div class="c-doctor-details">
                @if($doctor->specialization)
                    <span class="specialty">{{ $doctor->specialization }}</span>
                @else
                    <span class="specialty missing-data">
                        <i class="fas fa-question-circle"></i>
                        Specialty not specified
                    </span>
                @endif
                @if($doctor->city)
                    <span class="location">{{ $doctor->city }}</span>
                @else
                    <span class="location missing-data">
                        <i class="fas fa-question-circle"></i>
                        Location not provided
                    </span>
                @endif
            </div>
            @if($doctor->working_location)
                <div class="c-doctor-workplace">
                    <i class="fas fa-hospital"></i>
                    <span>{{ $doctor->working_location }}</span>
                </div>
            @else
                <div class="c-doctor-workplace missing-data">
                    <i class="fas fa-question-circle"></i>
                    <span>Workplace not specified</span>
                </div>
            @endif
            @if($doctor->description)
                <div class="c-doctor-education">
                    <i class="fas fa-graduation-cap"></i>
                    <span>{{ Str::limit($doctor->description, 60) }}</span>
                </div>
            @else
                <div class="c-doctor-education missing-data">
                    <i class="fas fa-question-circle"></i>
                    <span>Description not provided</span>
                </div>
            @endif
        </div>
    </div>
    @empty
    <div class="no-results">No Doctor Found</div>
    @endforelse
</div>

@if($doctors->hasPages())
<div class="pagination">
    @if($doctors->onFirstPage())
        <button class="prev-page" disabled><i class="fas fa-chevron-left"></i></button>
    @else
        <a href="{{ $doctors->previousPageUrl() }}" class="prev-page"><i class="fas fa-chevron-left"></i></a>
    @endif

    <div class="page-numbers">
        @php
            $maxVisible = 15;
            $end = min($doctors->lastPage(), $maxVisible);
        @endphp
        @foreach($doctors->getUrlRange(1, $end) as $page => $url)
            @if($page == $doctors->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $url }}"><span>{{ $page }}</span></a>
            @endif
        @endforeach
    </div>

    @if($doctors->hasMorePages())
        <a href="{{ $doctors->nextPageUrl() }}" class="next-page"><i class="fas fa-chevron-right"></i></a>
    @else
        <button class="next-page" disabled><i class="fas fa-chevron-right"></i></button>
    @endif
</div>
<div class="show-more-container">
    @if($doctors->hasMorePages())
        <a href="{{ $doctors->nextPageUrl() }}" class="show-more">Show More</a>
    @else
        <span class="show-more disabled">Show More</span>
    @endif
</div>
@endif
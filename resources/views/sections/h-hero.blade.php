<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Browse Our Best Specialists And<br>Schedule Video Consultations</h1>
        <form class="search-container" method="GET" action="{{ route('category') }}">
            <input type="text" name="search" placeholder="Search doctors, categories, topics..." class="search-input" value="{{ request('search') }}">
            <button type="submit" class="search-btn" aria-label="Search">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <p class="subtitle">More than 5000+ specialists</p>
    </div>
</section>
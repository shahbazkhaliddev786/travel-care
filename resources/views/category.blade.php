@extends('layouts.mainLayout')
@section('title', 'Category')
@section('css')
<link rel="stylesheet" href="{{ asset('css/category.css') }}">
@endsection

@section('content')


    <!-- Main Content -->
    <main class="main-content">
        <!-- Filter Section -->
        <div class="filter-container">
            <!-- Mobile Filter Toggle -->
            <button class="filter-toggle" id="filterToggle">
                <span class="filter-text">Filter</span>
                <i class="fas fa-chevron-down filter-icon"></i>
            </button>
            
            <!-- Desktop Filter Header -->
            <div class="section-header desktop-only">
                <h1>Filter</h1>
            </div>
            
            <aside class="filter-section" id="filterSection">
                <form method="GET" action="{{ isset($specialtySlug) && $specialtySlug ? route('category.specialty', ['specialty' => $specialtySlug]) : route('category') }}" class="filters-form">
                <!-- Location Filter -->
                <div class="filter-group">
                    <label>Location</label>
                    <div class="filter-field">
                        <input type="text" name="location" placeholder="All locations" value="{{ request('location') }}">
                    </div>
                </div>

                <!-- Hospital Filter -->
                <div class="filter-group">
                    <label>Hospital or Clinic</label>
                    <div class="filter-field">
                        <input type="text" name="hospital" placeholder="Start writing a hospital" value="{{ request('hospital') }}">
                    </div>
                </div>

                <!-- Education Filter -->
                <div class="filter-group">
                    <label>Education</label>
                    <div class="filter-field">
                        <input type="text" name="education" placeholder="Start writing an university" value="{{ request('education') }}">
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="filter-group">
                    <label>Consultation Price</label>
                    <div class="price-fields">
                        <div class="filter-field price-field">
                            <input type="number" name="min-price" placeholder="0$" value="{{ request('min-price') }}" step="1" min="0">
                            <span>Min</span>
                        </div>
                        <div class="filter-field price-field">
                            <input type="number" name="max-price" placeholder="0$" value="{{ request('max-price') }}" step="1" min="0">
                            <span>Max</span>
                        </div>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="button" class="reset-filters" id="resetFilters">Reset all filters</button>
                </div>
                </form>
            </aside>
        </div>

        <!-- Doctors List Section -->
        <section class="doctors-section">
            <div class="section-header">
                <h1>{{ $specialty }}</h1>
                <div class="sort-by">
                    <span>Nearest to Me</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>

            <!-- Doctors List -->
            <div id="resultsContainer">
                @include('partials.category-doctors', ['doctors' => $doctors])
            </div>
        </section>
    </main>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('filterToggle');
    const filterSection = document.getElementById('filterSection');
    const filterIcon = filterToggle?.querySelector('.filter-icon');
    
    // Check if we're in mobile/tablet view
    function isMobileView() {
        return window.innerWidth <= 992;
    }
    
    function initializeFilters() {
        if (!filterSection || !filterToggle) return;
        
        if (isMobileView()) {
            // Hide filters by default on mobile/tablet
            filterSection.classList.add('js-hidden');
            filterToggle.classList.remove('active');
            if (filterIcon) {
                filterIcon.style.transform = 'rotate(0deg)';
            }
        } else {
            // Show filters on desktop
            filterSection.classList.remove('js-hidden');
            filterToggle.classList.remove('active');
            if (filterIcon) {
                filterIcon.style.transform = 'rotate(0deg)';
            }
        }
    }
    
    function toggleFilters() {
        if (!filterSection || !filterToggle || !isMobileView()) return;
        
        const isHidden = filterSection.classList.contains('js-hidden');
        
        if (isHidden) {
            // Show filters
            filterSection.classList.remove('js-hidden');
            filterToggle.classList.add('active');
            if (filterIcon) {
                filterIcon.style.transform = 'rotate(180deg)';
            }
        } else {
            // Hide filters
            filterSection.classList.add('js-hidden');
            filterToggle.classList.remove('active');
            if (filterIcon) {
                filterIcon.style.transform = 'rotate(0deg)';
            }
        }
    }
    
    // Initialize on load
    initializeFilters();
    
    // Toggle functionality - only works in mobile view
    if (filterToggle) {
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Only toggle if we're in mobile view
            if (isMobileView()) {
                toggleFilters();
            }
        });
    }
    
    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            initializeFilters();
        }, 150);
    });
    
    // Add smooth transition effect
    if (filterSection) {
        filterSection.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    }

    // Reset filters button behavior
    const resetBtn = document.getElementById('resetFilters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Clear all inputs
            const inputs = document.querySelectorAll('.filters-form input');
            inputs.forEach(input => {
                input.value = '';
            });
            // Trigger refresh
            refreshResults();
        });
    }

    // Prevent form submission reloading the page
    const filtersForm = document.querySelector('.filters-form');
    if (filtersForm) {
        filtersForm.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    }

    // Instant filtering without reload
    const baseUrl = '{{ isset($specialtySlug) && $specialtySlug ? route('category.specialty', ['specialty' => $specialtySlug]) : route('category') }}';
    const inputs = document.querySelectorAll('.filters-form input');
    let debounceTimer;

    inputs.forEach(input => {
        const trigger = () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                refreshResults();
            }, 250);
        };
        input.addEventListener('input', trigger);
        input.addEventListener('change', trigger);
    });

    function getQueryParams() {
        // Start with existing query params to preserve non-filter values (e.g., search)
        const params = new URLSearchParams(window.location.search);
        // Overlay filters from inputs
        inputs.forEach(input => {
            const name = input.name;
            const value = input.value.trim();
            if (value.length > 0) {
                params.set(name, value);
            } else {
                params.delete(name);
            }
        });
        return params;
    }

    async function refreshResults(urlOverride) {
        const params = getQueryParams();
        // Always reset to first page when filters change
        if (!urlOverride) {
            params.set('page', '1');
        }
        const url = urlOverride ? urlOverride : `${baseUrl}?${params.toString()}`;
        try {
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });

            const container = document.getElementById('resultsContainer');
            const contentType = res.headers.get('content-type') || '';
            let htmlStr = '';
            if (contentType.includes('application/json')) {
                const data = await res.json();
                htmlStr = data && data.html ? data.html : '';
            } else {
                htmlStr = await res.text();
            }

            if (htmlStr) {
                // Update URL without reload for better UX
                window.history.replaceState(null, '', url);

                const tmp = document.createElement('div');
                tmp.innerHTML = htmlStr;
                const newList = tmp.querySelector('.doctors-list');
                const newPagination = tmp.querySelector('.pagination');
                const currentList = container.querySelector('.doctors-list');
                const currentPagination = container.querySelector('.pagination');
                if (newList && currentList) {
                    currentList.replaceWith(newList);
                }
                if (currentPagination && !newPagination) {
                    currentPagination.remove();
                } else if (!currentPagination && newPagination) {
                    container.appendChild(newPagination);
                } else if (currentPagination && newPagination) {
                    currentPagination.replaceWith(newPagination);
                }
                bindPagination();
            }
        } catch (err) {
            console.error('Failed to refresh results', err);
        }
    }

    function bindPagination() {
        const container = document.getElementById('resultsContainer');
        const pagination = container.querySelector('.pagination');
        const showMore = container.querySelector('.show-more-container a');
        if (pagination) {
            pagination.querySelectorAll('a').forEach(a => {
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.href;
                    refreshResults(url);
                });
            });
        }
        if (showMore) {
            showMore.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                refreshResults(url);
            });
        }
    }

    // Bind pagination on initial load
    bindPagination();
});
</script>
@endsection
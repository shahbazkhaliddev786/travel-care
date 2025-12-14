{{-- Search & Filter Component --}}
<div class="card mb-4">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="bi bi-funnel me-2"></i>
            Search & Filter
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ $action }}" class="row g-3" id="filterForm">
            <div class="col-lg-4 col-md-6">
                <label for="search" class="form-label">{{ $searchLabel }}</label>
                <div class="enhanced-search">
                    <input type="text" 
                           class="form-control" 
                           id="search"
                           name="search" 
                           placeholder="{{ $searchPlaceholder }}" 
                           value="{{ request('search') }}">
                    <i class="bi bi-search search-icon"></i>
                </div>
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status">
                    <option value="">All Status ({{ $stats['total'] }})</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved ({{ $stats['approved'] }})</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending ({{ $stats['pending'] }})</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected ({{ $stats['rejected'] }})</option>
                </select>
            </div>
            
            @if($showVideoConsultation ?? false)
            <div class="col-md-3">
                <label for="video_consult" class="form-label">Video Consultation</label>
                <select class="form-select" name="video_consult" id="video_consult">
                    <option value="">All ({{ $stats['total'] }})</option>
                    <option value="{{ $videoEnabledValue ?? 'yes' }}" {{ request('video_consult') == ($videoEnabledValue ?? 'yes') ? 'selected' : '' }}>Enabled ({{ $stats['video_enabled'] }})</option>
                    <option value="{{ $videoDisabledValue ?? 'no' }}" {{ request('video_consult') == ($videoDisabledValue ?? 'no') ? 'selected' : '' }}>Disabled ({{ $stats['total'] - $stats['video_enabled'] }})</option>
                </select>
            </div>
            @endif
            
            <div class="col-md-3">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" 
                       class="form-control" 
                       name="date_from" 
                       id="date_from"
                       value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-3">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" 
                       class="form-control" 
                       name="date_to" 
                       id="date_to"
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i>
                        Filter
                    </button>
                    <a href="{{ $action }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
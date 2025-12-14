@extends('layouts.mainLayout')

@section('title', $page->title)

@section('css')
<link rel="stylesheet" href="{{ asset('css/terms-conditions.css') }}">
@endsection

@section('content')
<main class="main-content">
    <div class="page-container">
        <div class="page-header">
            <h1 class="page-title">{{ $page->title }}</h1>
        </div>
        
        @if(isset($isMissing) && $isMissing)
            <div class="page-content">
                <div class="alert alert-info text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-info-circle-fill" style="font-size: 3rem; color: #0dcaf0;"></i>
                    </div>
                    <h4 class="alert-heading">Content Not Available</h4>
                    <p class="mb-0">The content for this page is not available at the moment. Please check back later or contact support if you need assistance.</p>
                </div>
            </div>
        @elseif(isset($isDeactivated) && $isDeactivated)
            <div class="page-content">
                <div class="alert alert-warning text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #f0ad4e;"></i>
                    </div>
                    <h4 class="alert-heading">Page Temporarily Unavailable</h4>
                    <p class="mb-0">This page is currently deactivated and not available for viewing. Please check back later or contact support if you need assistance.</p>
                </div>
            </div>
        @else
            <div class="page-content">
                <div class="content-section">
                    {!! $page->content !!}
                </div>
            </div>

            @if($page->last_updated_by)
            <div class="last-updated">
                <p>Last updated: {{ $page->last_updated_by->format('F j, Y') }}</p>
            </div>
            @endif
        @endif
    </div>
</main>
@endsection
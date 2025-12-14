@extends('layouts.mainLayout')

@section('title', 'Doctor Profile')
@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/doctor-profile.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile/doctor-profile-responsive.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/profile-image-modal.css') }}">
@endsection

@section('content')
    <div class="container">
        <!-- Left Side -->
        @include('partials.doctor_profile_sidebar')

        <!-- Right Side -->
        @include('partials.doctor_services')
    </div>
    
    <!-- Edit Profile Modal -->
    @include('partials.doctor_edit_profile_modal')
    
    <!-- Payment Methods Modal -->
    @include('partials.doctor_payment_methods_modal')
    
    <!-- Add Card Modal -->
    @include('partials.doctor_add_card_modal')
    
    <!-- Profile Image Modal -->
    @include('partials.profile_image_modal')
@endsection

@section('script')
<script src="{{ asset('js/profile/doctor-profile.js') }}"></script>
<script src="{{ asset('js/components/profile-image-modal.js') }}"></script>
@endsection
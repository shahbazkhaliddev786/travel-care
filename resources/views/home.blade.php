@extends('layouts.mainLayout')
@section('title', 'Home')
@section('css')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
    @include('sections.h-hero')
    @include('sections.h-specialties')

    @include('sections.h-get-care')
    @include('sections.h-top-doctors')
@endsection
 
@section('script')
<script src="{{ asset('js/home.js') }}"></script>
@endsection

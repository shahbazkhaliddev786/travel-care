@extends('layouts.getstartedlayout')

@section('title', 'TravelCare - Sign Up')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <a href="#" id="prevButton"><img src="/assets/icons/arrow-left.svg" alt="return" class="arr-back"></a>
            
            
            <!-- Customer Sign Up Content 2nd Box-->
            <div class="signin-content" id="box2">
                <h2 class="title secondary">General Information</h2>
                <p>We need to ask you basic information to start</p>

                <!-- General Information Form -->
                <form class="signin-form" action="{{ route('c-info', ['p_name' => 'verify-number']) }}" method="POST">
                    @csrf
                    <div class="label-input">
                        <label for="">Country</label>
                        <x-input-field type="text" name="country" placeholder="Country" />
                    </div>
                    <div class="label-input">
                        <label for="">City Or village</label>
                        <x-input-field type="text" name="city" placeholder="City Or village" />
                    </div>
                    <div class="label-input">
                        <label for="">Biological Sex</label>
                        <div class="input-group gender-group">
                            <input type="hidden" name="gender" id="gender" value="Female">
                            <button type="button" class="btn btn-primary gender active" data-value="Female">Female</button>
                            <button type="button" class="btn btn-primary gender" data-value="Male">Male</button>
                        </div>
                    </div>
                    <div class="label-input">
                        <label for="">Your Age</label>
                        <x-input-field type="number" name="age" placeholder="Your Age" />
                    </div>
                    <div class="label-input">
                        <label for="">Your Weight</label>
                        <x-input-field type="number" name="weight" placeholder="Your Weight" />
                    </div>
                    <div class="label-input">
                        <label for="">Chronic Pathologies</label>
                        <div class="input-group">
                            <input type="text" name="chronic_pathologies[]" placeholder="E.G. Diabetes" required>
                        </div>
                        <button type="button" class="btn dotted-btn">+ Add More</button>
                    </div>
                    <div class="label-input">
                        <label for="">Allergies</label>
                        <div class="input-group">
                            <input type="text" name="allergies[]" placeholder="Allergies" required>
                        </div>
                        <button type="button" class="btn dotted-btn">+ Add More</button>
                    </div>
                    <div class="label-input">
                        <label for="">Chronic Medication</label>
                        <div class="input-group">
                            <input type="text" name="chronic_medications[]" placeholder="Chronic Medication" required>
                        </div>
                        <button type="button" class="btn dotted-btn">+ Add More</button>
                    </div>
                    <button class="btn btn-primary">Next</button>
                </form>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/partials/dynamic-toggle.js') }}"></script>
@endsection
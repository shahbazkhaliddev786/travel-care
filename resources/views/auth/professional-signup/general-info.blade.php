@extends('layouts.getstartedlayout')

@section('title', 'TravelCare - Sign Up')

@section('content')
    <div class="form-container">
        <div class="auth-content-box">
            <a href="{{ route('p-signup') }}" id="prevButton"><img src="/assets/icons/arrow-left.svg" alt="return" class="arr-back"></a>
            
            
            <!-- Customer Sign Up Content 2nd Box-->
            <div class="signin-content" id="box2">
                <h2 class="title secondary">General Information</h2>
                <p>We need to ask you basic information to start</p>

                <!-- General Information Form -->
                <form class="signin-form" action="{{ route('p-info') }}" method="POST">
                    @csrf
                    <div class="label-input">
                        <label for="">Account type</label>
                        <div class="input-group gender-group">
                            <input type="hidden" name="acc-type" id="acc-type" value="Doctor">
                            <button type="button" class="btn btn-primary gender active" data-value="Doctor">Doctor</button>
                            <button type="button" class="btn btn-primary gender" data-value="Laboratory">Laboratory</button>
                        </div>
                    </div>
                    <div class="label-input">
                        <label for="">City or village</label>
                        <x-input-field name="city" type="text" placeholder="City or Village" />
                    </div>

                    <div class="label-input">
                        <label for="">Name</label>
                        <x-input-field type="text" name="name" placeholder="Full Name" />
                    </div>

                    <div class="label-input">
                        <label for="">Professional Mexican ID</label>
                        <x-input-field type="text" name="p-id" placeholder="Professional Mexican ID" />
                    </div>

                    <div class="label-input">
                        <label for="">Mexican Voting License Scan</label>
                        <x-input-field type="text" name="license" placeholder="Voting License Scan" />
                    </div>

                    <div class="label-input">
                        <label for="">Address</label>
                        <x-input-field type="text" name="address" placeholder="Address" />
                    </div>
                    <button class="btn btn-primary">Create account</button>
                </form>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle gender selection
        const genderButtons = document.querySelectorAll('.gender');
        const genderInput = document.getElementById('acc-type');
        
        genderButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                // Remove active class from all buttons
                genderButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                // Update hidden input value
                genderInput.value = this.getAttribute('data-value');
            });
        });
    });

</script>
@endsection
@extends('layouts.mainLayout')
@section('title', 'Payment')
@section('css')
<link rel="stylesheet" href="{{ asset('css/payment.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/payment-popup.css') }}">
@endsection

@section('head-scripts')
<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
@endsection

@section('content')

<!-- Main Content -->
<main class="main-content">
    <!-- Payment Container -->
    <div class="payment-container">
        <!-- Appointment Overview -->
        <div class="overview-section">
            <h2>Appointment Overview</h2>
            <div class="overview-details">
                <div class="detail-row">
                    <span class="label">Doctor:</span>
                    <span class="value" id="doctor-name">Dr. Martha Zoldana</span>
                </div>
                <div class="detail-row">
                    <span class="label">Service:</span>
                    <span class="value" id="service-type">House Visit</span>
                </div>
                <div class="detail-row">
                    <span class="label">Date:</span>
                    <span class="value" id="appointment-date">Mo, 17.08.2023</span>
                </div>
                <div class="detail-row">
                    <span class="label">Time:</span>
                    <span class="value" id="appointment-time">8:00 AM — 8:30 AM</span>
                </div>
                <div class="detail-row" id="location-row">
                    <span class="label">Location:</span>
                    <span class="value" id="appointment-location">Jose Maria Morelos No.48, Montelon Centro, Mexico</span>
                </div>
                <div class="detail-row total">
                    <span class="label">Total Fee:</span>
                    <span class="value" id="total-fee">$300.00</span>
                </div>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="payment-section">
            <h2>Payment Method</h2>
            
            <!-- PayPal Option -->
            <div class="payment-option">
                <label class="payment-label">
                    <input type="radio" name="payment" value="paypal">
                    <span class="radio-custom"></span>
                    <div class="payment-info">
                        <span>PayPal</span>
                        <p>You will be redirected to the PayPal website after submitting your order</p>
                    </div>
                    <img src="/assets/images/card5.png" alt="PayPal" class="payment-logo">
                </label>
            </div>

            <!-- User's Saved Cards -->
            @if(count($paymentMethods) > 0)
                @foreach($paymentMethods as $paymentMethod)
                    <div class="payment-option">
                        <label class="payment-label">
                            <input type="radio" name="payment" value="card_{{ $paymentMethod->id }}">
                            <span class="radio-custom"></span>
                            <div class="payment-info">
                                <span>{{ $paymentMethod->card_holder }}</span>
                                <p>{{ $paymentMethod->masked_number }}</p>
                            </div>
                            @if($paymentMethod->card_type == 'visa')
                                <img src="/assets/images/card1.png" alt="Visa" class="payment-logo">
                            @elseif($paymentMethod->card_type == 'mastercard')
                                <img src="/assets/images/card4.png" alt="Mastercard" class="payment-logo">
                            @else
                                <img src="/assets/images/card1.png" alt="Card" class="payment-logo">
                            @endif
                        </label>
                    </div>
                @endforeach
            @endif

            <!-- Add New Card -->
            <div class="add-card-section">
                <button class="add-card-btn" id="addNewCardBtn">
                    <i class="fas fa-plus"></i>
                    Add a New Card
                    <div class="card-types">
                        <img src="/assets/images/card1.png" alt="Visa">
                        <img src="/assets/images/card2.png" alt="Discover">
                        <img src="/assets/images/card3.png" alt="Mastercard">
                        <img src="/assets/images/card4.png" alt="Maestro">
                    </div>
                </button>
            </div>

            <!-- New Card Form with Stripe Elements -->
            <div class="new-card-form hidden" id="newCardForm">
                <h3>Add New Card</h3>
                <form id="payment-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="card-element">Card Information</label>
                            <div id="card-element">
                                <!-- Stripe Elements will create form elements here -->
                            </div>
                            <div id="card-errors" role="alert" class="error-message"></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cardHolderName">Card Holder Name</label>
                            <input type="text" id="cardHolderName" placeholder="Card Holder Name" required>
                        </div>
                    </div>
                    <div class="save-card">
                        <label class="checkbox-label">
                            <input type="checkbox" id="saveCard" checked>
                            <span class="checkbox-custom"></span>
                            Save card for future payments
                        </label>
                    </div>
                </form>
            </div>

            <!-- Legacy Card Form (fallback) -->
            <div class="legacy-card-form hidden" id="legacyCardForm">
                <h3>Add New Card (Legacy)</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="legacyCardNumber">Enter Card Number</label>
                        <input type="text" id="legacyCardNumber" placeholder="Card Number" maxlength="19">
                    </div>
                    <div class="form-group">
                        <label for="legacyCardHolder">Card Holder Name</label>
                        <input type="text" id="legacyCardHolder" placeholder="Card Holder Name">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Expire Date</label>
                        <div class="expire-inputs">
                            <input type="text" id="legacyExpiryMonth" placeholder="MM" maxlength="2">
                            <input type="text" id="legacyExpiryYear" placeholder="YY" maxlength="2">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="legacyCvv">CVV Code</label>
                        <input type="text" id="legacyCvv" placeholder="CVV" maxlength="4">
                    </div>
                </div>
                <div class="save-card">
                    <label class="checkbox-label">
                        <input type="checkbox" id="legacySaveCard" checked>
                        <span class="checkbox-custom"></span>
                        Save card for future payments
                    </label>
                </div>
            </div>

            <!-- Terms and Action Buttons -->
            <div class="terms-section">
                <p>By adding a card, you agree to the <a href="#">Terms & Conditions</a></p>
            </div>

            <div class="action-buttons">
                <button class="btn-cancel" id="cancelPaymentBtn">Cancel Payment</button>
                <button class="btn-pay" id="payNowBtn">Pay Now</button>
            </div>

            <!-- Loading overlay -->
            <div id="loadingOverlay" class="loading-overlay hidden">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Processing payment...</p>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection

@section('script')

<script>
    // Pass Stripe publishable key to JavaScript
    window.stripePublishableKey = '{{ $stripePublishableKey }}';
    
    // Debug Stripe loading
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Debug: Stripe initialization check...');
        console.log('- Stripe available:', typeof Stripe !== 'undefined');
        console.log('- Publishable key:', window.stripePublishableKey ? 'Set' : 'Missing');
        
        if (typeof Stripe === 'undefined') {
            console.error('Stripe.js failed to load. Check your internet connection.');
        } else {
            console.log('✓ Stripe.js loaded successfully');
        }
    });
</script>
<script src="{{ asset('js/components/payment-popup.js') }}"></script>
<script src="{{ asset('js/payment.js') }}"></script>

@endsection
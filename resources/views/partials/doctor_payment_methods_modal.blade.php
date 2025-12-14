<div class="modal" id="paymentMethodsModal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="modal-body">
            <h2 class="form-title">My Payment Methods</h2>
            <div class="payment-section">
                <h3>My Bank Cards</h3>
                
                <div class="saved-cards">
                    @if(isset($doctor->payment_methods) && is_array($doctor->payment_methods) && count($doctor->payment_methods) > 0)
                        @foreach($doctor->payment_methods as $card)
                            <div class="card-item">
                                <div class="card-info">
                                    <div class="card-logo">
                                        @if($card['type'] == 'visa')
                                            <img src="{{ asset('assets/images/card1.png') }}" alt="Visa">
                                        @elseif($card['type'] == 'mastercard')
                                            <img src="{{ asset('assets/images/card4.png') }}" alt="Mastercard">
                                        @else
                                            <img src="{{ asset('assets/images/card1.png') }}" alt="Card">
                                        @endif
                                    </div>
                                    <div class="card-details">
                                        <div class="card-holder">{{ $card['holder_name'] }}</div>
                                        <div class="card-number">xxxx xxxx xxxx {{ $card['last_four'] }}</div>
                                    </div>
                                </div>
                                <form action="{{ route('profile.delete-payment-method', $card['id']) }}" method="POST" class="delete-card-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-card-btn">Delete</button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <div class="no-services-message">
                            <p>No Cards Added</p>
                        </div>
                    @endif
                </div>
                
                <button class="btn btn-primary" id="addBankCardBtn">Add New Bank Card</button>
            </div>
            
            <div class="payment-section">
                <h3>PayPal Payment</h3>
                
                <form action="{{ route('profile.update-paypal-email') }}" method="POST" class="paypal-form" id="paypal-email-form">
                    @csrf
                    @method('PUT')
                    <input type="email" id="paypal_email" name="paypal_email" placeholder="PayPal Email" class="form-input" value="{{ $doctor->paypal_email ?? '' }}" required>
                    <button type="submit" class="btn btn-primary" id="addPaypalBtn">Save PayPal Email</button>
                </form>
            </div>
        </div>
    </div>
</div>
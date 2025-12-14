<div class="modal" id="addCardModal">
    <div class="modal-content">
        <span class="close-modal" id="closeAddCardModal">&times;</span>

        <div class="modal-body">
            <h2 class="form-title">My Payment Methods</h2>
            
            <form action="{{ route('profile.add-payment-method') }}" method="POST" id="add-card-form">
                @csrf
                <h3 class="form-title">Add New Card</h3>
                
                <div class="data-fields">
                    <div class="form-group">
                        <label for="card_number">Enter Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="Card Number" class="form-input" maxlength="16" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="holder_name">Card Holder Name</label>
                        <input type="text" id="holder_name" name="holder_name" placeholder="Card Holder Name" class="form-input" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="expiry_date">Expire Date</label>
                            <div class="expiry-inputs">
                                <input type="text" id="expiry_month" name="expiry_month" placeholder="MM" class="form-input" maxlength="2" required>
                                <input type="text" id="expiry_year" name="expiry_year" placeholder="YY" class="form-input" maxlength="2" required>
                            </div>
                        </div>
                        
                        <div class="form-group half">
                            <label for="cvv">CVV Code</label>
                            <input type="text" id="cvv" name="cvv" placeholder="CVV" class="form-input" maxlength="4" required>
                        </div>
                    </div>
                    
                    <input type="hidden" name="card_type" id="card_type" value="visa">
                    
                    <button type="submit" class="btn btn-primary">Add New Payment Method</button>
                    
                    <div class="terms-text">
                        By adding a card, you agree to the <br>
                        <a href="#" class="terms-link">Terms & Conditions</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
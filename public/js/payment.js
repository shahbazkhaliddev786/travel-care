document.addEventListener('DOMContentLoaded', function() {
    // Check if Stripe is available
    if (typeof Stripe === 'undefined') {
        console.error('Stripe.js not loaded. Please check your internet connection or reload the page.');
        // Show user-friendly error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'stripe-error-message';
        errorDiv.innerHTML = `
            <div style="background: #f56565; color: white; padding: 15px; border-radius: 8px; margin: 20px; text-align: center;">
                <strong>Payment system unavailable</strong><br>
                Unable to load Stripe.js. Please check your internet connection and reload the page.
            </div>
        `;
        const paymentContainer = document.querySelector('.payment-container');
        if (paymentContainer) {
            paymentContainer.prepend(errorDiv);
        }
        return;
    }

    // Check if publishable key is available
    if (!window.stripePublishableKey) {
        console.error('Stripe publishable key not found.');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'stripe-key-error-message';
        errorDiv.innerHTML = `
            <div style="background: #f56565; color: white; padding: 15px; border-radius: 8px; margin: 20px; text-align: center;">
                <strong>Payment configuration error</strong><br>
                Stripe publishable key is missing. Please contact support.
            </div>
        `;
        const paymentContainer = document.querySelector('.payment-container');
        if (paymentContainer) {
            paymentContainer.prepend(errorDiv);
        }
        return;
    }

    // Initialize Stripe (publishable key will be set via PHP)
    const stripe = Stripe(window.stripePublishableKey);
    const elements = stripe.elements();

    // Create card element
    const card = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#9e2146',
            },
        },
    });

    // Payment method selection
    const paymentOptions = document.querySelectorAll('.payment-label input[type="radio"]');
    const addCardBtn = document.querySelector('#addNewCardBtn');
    const newCardForm = document.querySelector('#newCardForm');
    const legacyCardForm = document.querySelector('#legacyCardForm');
    const payButton = document.querySelector('#payNowBtn');
    const cancelButton = document.querySelector('#cancelPaymentBtn');
    const loadingOverlay = document.querySelector('#loadingOverlay');

    // Legacy card form elements (for fallback)
    const cardNumberInput = document.querySelector('#legacyCardNumber');
    const cardHolderInput = document.querySelector('#legacyCardHolder');
    const expiryInputs = [
        document.querySelector('#legacyExpiryMonth'),
        document.querySelector('#legacyExpiryYear')
    ].filter(Boolean); // Remove null elements
    const cvvInput = document.querySelector('#legacyCvv');

    // Payment processing state
    let isProcessingPayment = false;
    let selectedPaymentMethod = null;
    let paymentIntentId = null;

    // Mount card element
    if (document.getElementById('card-element')) {
        card.mount('#card-element');
    }

    // Handle card element changes
    card.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
            displayError.style.display = 'block';
        } else {
            displayError.textContent = '';
            displayError.style.display = 'none';
        }
    });

    // Format Card Number
    function formatCardNumber(value) {
        const v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        const matches = v.match(/\d{4,16}/g);
        const match = matches && matches[0] || '';
        const parts = [];

        for (let i = 0, len = match.length; i < len; i += 4) {
            parts.push(match.substring(i, i + 4));
        }

        if (parts.length) {
            return parts.join(' ');
        } else {
            return value;
        }
    }

    // Handle Card Number Input (Legacy form)
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            this.value = formatCardNumber(this.value);
        });
    }

    // Handle Expiry Date Inputs (Legacy form)
    if (expiryInputs && expiryInputs.length > 0) {
        expiryInputs.forEach(input => {
            if (input) {
                input.addEventListener('input', function(e) {
                    if (this.value.length >= 2) {
                        const next = this.nextElementSibling;
                        if (next && next.tagName === 'INPUT') {
                            next.focus();
                        }
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value) {
                        const prev = this.previousElementSibling;
                        if (prev && prev.tagName === 'INPUT') {
                            prev.focus();
                        }
                    }
                });
            }
        });
    }

    // Handle CVV Input (Legacy form)
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substring(0, 4);
        });
    }

    // Toggle New Card Form
    if (addCardBtn) {
        addCardBtn.addEventListener('click', function() {
            newCardForm.classList.toggle('hidden');
            if (!newCardForm.classList.contains('hidden')) {
                // Focus on the card holder name input for Stripe Elements form
                const cardHolderName = document.getElementById('cardHolderName');
                if (cardHolderName) {
                    cardHolderName.focus();
                } else if (cardNumberInput) {
                    // Fallback to legacy card number input
                    cardNumberInput.focus();
                }
            }
        });
    }

    // Handle Payment Option Selection
    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            // If new card is selected, show the form
            if (this.value === 'new_card') {
                newCardForm.classList.remove('hidden');
            } else {
                newCardForm.classList.add('hidden');
            }
        });
    });

    // Validate Form
    function validateForm() {
        let isValid = true;
        const selectedPayment = document.querySelector('.payment-label input[type="radio"]:checked');

        if (!selectedPayment) {
            alert('Please select a payment method');
            return false;
        }

        // For Stripe Elements, validation is handled by Stripe itself
        // This function is mainly for legacy form validation
        if (!newCardForm.classList.contains('hidden') && cardNumberInput) {
            // Validate card number (legacy)
            if (cardNumberInput && cardNumberInput.value.replace(/\s/g, '').length < 16) {
                cardNumberInput.classList.add('error');
                isValid = false;
            }

            // Validate expiry date (legacy)
            if (expiryInputs && expiryInputs.length >= 2 && expiryInputs[0] && expiryInputs[1]) {
                const month = parseInt(expiryInputs[0].value);
                const year = parseInt(expiryInputs[1].value);
                const currentYear = new Date().getFullYear() % 100;
                const currentMonth = new Date().getMonth() + 1;

                if (!month || month < 1 || month > 12 || !year ||
                    (year < currentYear || (year === currentYear && month < currentMonth))) {
                    expiryInputs.forEach(input => {
                        if (input) input.classList.add('error');
                    });
                    isValid = false;
                }
            }

            // Validate CVV (legacy)
            if (cvvInput && cvvInput.value.length < 3) {
                cvvInput.classList.add('error');
                isValid = false;
            }
        }

        return isValid;
    }

    // Pay button click handler
    function attachPayButtonHandler() {
        const payBtn = document.querySelector('#payNowBtn');
        if (payBtn) {
            console.log('Attaching Pay Now button handler');
            payBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                console.log('Pay Now button clicked');
                
                if (isProcessingPayment) {
                    console.log('Payment already in progress, ignoring click');
                    return;
                }
                
                await handlePayment();
            });
        } else {
            console.error('Pay Now button not found');
        }
    }

    // Attach pay button handler
    attachPayButtonHandler();

    /**
     * Handle payment processing
     */
    async function handlePayment() {
        try {
            console.log('Starting payment processing...');
            hideError();
            
            // Check if we have a selected payment method or new card form is visible
            const selectedPayment = document.querySelector('.payment-label input[type="radio"]:checked');
            const isNewCardVisible = newCardForm && !newCardForm.classList.contains('hidden');
            
            console.log('Selected payment method:', selectedPayment?.value);
            console.log('New card form visible:', isNewCardVisible);
            
            if (!selectedPayment && !isNewCardVisible) {
                showError('Please select a payment method or add a new card.');
                return;
            }

            const appointmentData = getAppointmentData();
            console.log('Appointment data for payment:', appointmentData);
            
            showLoading();

            // If using new card form
            if (isNewCardVisible) {
                await handleNewCardPayment(appointmentData);
            } else if (selectedPayment) {
                // Update selectedPaymentMethod for backwards compatibility
                selectedPaymentMethod = selectedPayment.value;
                await handleExistingCardPayment(appointmentData);
            } else {
                throw new Error('No payment method selected');
            }

        } catch (error) {
            console.error('Payment error:', error);
            hideLoading();
            
            const appointmentData = getAppointmentData();
            
            // Show failure popup instead of inline error
            if (window.paymentPopup) {
                paymentPopup.showFailure({
                    errorMessage: error.message || 'Payment failed. Please try again.',
                    amount: appointmentData.total_fee,
                    doctorName: appointmentData.doctor_name,
                    serviceType: appointmentData.service_type,
                    retryCallback: () => {
                        // Retry payment function
                        setTimeout(() => {
                            handlePayment();
                        }, 500);
                    },
                    autoClose: false
                });
            } else {
                // Fallback to existing error display
                showError(error.message || 'An unexpected error occurred during payment processing.');
            }
        }
    }

    // Cancel button
    function attachCancelButtonHandler() {
        const cancelBtn = document.querySelector('#cancelPaymentBtn');
        if (cancelBtn) {
            console.log('Attaching Cancel Payment button handler');
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Cancel Payment button clicked');
                if (confirm('Are you sure you want to cancel the payment and go back?')) {
                    // Clear session data
                    sessionStorage.removeItem('appointment_data');
                    // Go back to previous page or home
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        window.location.href = '/home';
                    }
                }
            });
        } else {
            console.error('Cancel Payment button not found');
        }
    }

    // Attach cancel button handler
    attachCancelButtonHandler();

    /**
     * Handle payment with new card
     */
    async function handleNewCardPayment(appointmentData) {
        const cardHolderName = document.getElementById('cardHolderName')?.value?.trim();
        const saveCard = document.getElementById('saveCard')?.checked || false;

        if (!cardHolderName) {
            throw new Error('Please enter the card holder name.');
        }

        // Create payment method with Stripe
        const { error, paymentMethod } = await stripe.createPaymentMethod({
            type: 'card',
            card: card,
            billing_details: {
                name: cardHolderName,
            },
        });

        if (error) {
            throw new Error(error.message);
        }

        // Process payment with the new payment method
        await processPaymentWithMethod(paymentMethod.id, appointmentData, saveCard);
    }

    /**
     * Handle payment with existing card
     */
    async function handleExistingCardPayment(appointmentData) {
        if (!selectedPaymentMethod) {
            throw new Error('Please select a payment method.');
        }

        // For PayPal, show message for now
        if (selectedPaymentMethod === 'paypal') {
            showError('PayPal integration coming soon. Please use a credit card for now.');
            return;
        }

        // For saved cards, use the selected payment method
        await processPaymentWithMethod(selectedPaymentMethod, appointmentData, false);
    }

    /**
     * Process payment with Stripe
     */
    async function processPaymentWithMethod(paymentMethodId, appointmentData, saveCard = false) {
        // Prepare payment data with proper formatting
        const paymentData = {
            payment_method_id: paymentMethodId,
            amount: appointmentData.total_fee,
            service_type: appointmentData.service_type,
            appointment_date: appointmentData.appointment_date || null, // Use converted date
            appointment_time: appointmentData.appointment_time || appointmentData.time,
            location: appointmentData.location || null,
            save_payment_method: saveCard
        };

        // Add doctor_id if available
        if (appointmentData.doctor_id) {
            paymentData.doctor_id = appointmentData.doctor_id;
        }

        console.log('Sending payment data to server:', paymentData);

        const response = await fetch('/payment/process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(paymentData)
        });

        const result = await response.json();
        console.log('Server response:', result);

        if (!response.ok) {
            throw new Error(result.message || result.error || 'Payment processing failed');
        }

        if (result.success) {
            // Payment succeeded - show popup instead of redirecting
            hideLoading();
            
            const appointmentData = getAppointmentData();
            
            // Show success popup with transaction details
            if (window.paymentPopup) {
                paymentPopup.showSuccess({
                    transactionId: result.transaction_id,
                    amount: result.transaction.amount,
                    doctorName: result.transaction.doctor_name,
                    serviceType: result.transaction.service_type,
                    appointmentDate: result.transaction.appointment_date ? new Date(result.transaction.appointment_date).toLocaleDateString() : appointmentData.date,
                    appointmentTime: result.transaction.appointment_time || appointmentData.time,
                    location: result.transaction.location,
                    message: 'Your payment has been processed successfully! Your appointment is confirmed.',
                    autoClose: true,
                    autoCloseDelay: 8000
                });
            } else {
                // Fallback to alert if popup not available
                alert('Payment successful! Transaction ID: ' + result.transaction_id);
                window.location.href = '/home';
            }
            
            // Clear session data
            sessionStorage.removeItem('appointment_data');
            
        } else if (result.requires_action) {
            // Handle 3D Secure or other authentication
            await handle3DSecure(result.client_secret);
        } else {
            throw new Error('Payment failed: ' + (result.status || 'Unknown error'));
        }
    }

    /**
     * Handle 3D Secure authentication
     */
    async function handle3DSecure(clientSecret) {
        showLoading('Authenticating payment...');

        const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret);

        if (error) {
            throw new Error(error.message);
        }

        if (paymentIntent.status === 'succeeded') {
            hideLoading();
            
            const appointmentData = getAppointmentData();
            
            // Show success popup after 3D Secure authentication
            if (window.paymentPopup) {
                paymentPopup.showSuccess({
                    transactionId: paymentIntent.metadata?.transaction_id || 'N/A',
                    amount: paymentIntent.amount / 100, // Convert from cents
                    doctorName: appointmentData.doctor_name,
                    serviceType: appointmentData.service_type,
                    appointmentDate: appointmentData.date,
                    appointmentTime: appointmentData.time,
                    location: appointmentData.location,
                    message: 'Payment authenticated and processed successfully! Your appointment is confirmed.',
                    autoClose: true,
                    autoCloseDelay: 8000
                });
            } else {
                // Fallback to alert if popup not available
                alert('Payment successful! Your appointment is confirmed.');
                window.location.href = '/home';
            }
            
            // Clear session data
            sessionStorage.removeItem('appointment_data');
            
        } else {
            throw new Error('Payment authentication failed');
        }
    }

    /**
     * Show loading state
     */
    function showLoading(message = 'Processing payment...') {
        if (loadingOverlay) {
            loadingOverlay.querySelector('p').textContent = message;
            loadingOverlay.classList.remove('hidden');
        }
        
        if (payButton) {
            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        }
        
        isProcessingPayment = true;
    }

    /**
     * Hide loading state
     */
    function hideLoading() {
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }
        
        if (payButton) {
            payButton.disabled = false;
            payButton.innerHTML = 'Pay Now';
        }
        
        isProcessingPayment = false;
    }

    /**
     * Show error message
     */
    function showError(message) {
        // Create or update error alert
        let errorAlert = document.querySelector('.payment-error-alert');
        if (!errorAlert) {
            errorAlert = document.createElement('div');
            errorAlert.className = 'payment-error-alert alert alert-danger';
            errorAlert.style.cssText = `
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
                padding: 12px;
                border-radius: 4px;
                margin: 15px 0;
                display: flex;
                align-items: center;
            `;
            
            const paymentSection = document.querySelector('.payment-section');
            if (paymentSection) {
                paymentSection.insertBefore(errorAlert, paymentSection.firstChild);
            }
        }
        
        errorAlert.innerHTML = `<i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i> ${message}`;
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /**
     * Hide error message
     */
    function hideError() {
        const errorAlert = document.querySelector('.payment-error-alert');
        if (errorAlert) {
            errorAlert.remove();
        }
    }

    /**
     * Show success message
     */
    function showSuccess(message) {
        hideError(); // Remove any existing error
        
        let successAlert = document.querySelector('.payment-success-alert');
        if (!successAlert) {
            successAlert = document.createElement('div');
            successAlert.className = 'payment-success-alert alert alert-success';
            successAlert.style.cssText = `
                background-color: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
                padding: 12px;
                border-radius: 4px;
                margin: 15px 0;
                display: flex;
                align-items: center;
            `;
            
            const paymentSection = document.querySelector('.payment-section');
            if (paymentSection) {
                paymentSection.insertBefore(successAlert, paymentSection.firstChild);
            }
        }
        
        successAlert.innerHTML = `<i class="fas fa-check-circle" style="margin-right: 8px;"></i> ${message}`;
        successAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /**
     * Convert display date to server-compatible format
     */
    function convertDateForServer(displayDate) {
        if (!displayDate) {
            // Use today's date as fallback
            return new Date().toISOString().split('T')[0];
        }
        
        try {
            console.log('Converting date:', displayDate);
            
            // Handle different date formats
            // Format: "Mo, 17.08.2023" or similar
            if (displayDate.includes('.')) {
                const parts = displayDate.split(',')[1]?.trim().split('.');
                if (parts && parts.length === 3) {
                    const day = parts[0].padStart(2, '0');
                    const month = parts[1].padStart(2, '0');
                    const year = parts[2];
                    const converted = `${year}-${month}-${day}`;
                    console.log('Converted date with dots:', converted);
                    return converted;
                }
            }
            
            // Handle single day names or numbers (from appointment page)
            if (displayDate.length <= 3) {
                // If it's just a day like "Mo", "Tu", "17", etc.
                // Use today's date as we don't have enough info
                const today = new Date();
                const converted = today.toISOString().split('T')[0];
                console.log('Using today for short date:', converted);
                return converted;
            }
            
            // Try to parse as a standard date
            const date = new Date(displayDate);
            if (!isNaN(date.getTime())) {
                const converted = date.toISOString().split('T')[0]; // YYYY-MM-DD format
                console.log('Parsed as standard date:', converted);
                return converted;
            }
            
            // Handle YYYY-MM-DD format (already correct)
            if (/^\d{4}-\d{2}-\d{2}$/.test(displayDate)) {
                console.log('Already in correct format:', displayDate);
                return displayDate;
            }
            
            // Fallback to today's date
            console.warn('Could not parse date:', displayDate, 'using today');
            return new Date().toISOString().split('T')[0];
            
        } catch (error) {
            console.error('Error converting date:', error);
            return new Date().toISOString().split('T')[0];
        }
    }

    /**
     * Get appointment data from session storage or form
     */
    function getAppointmentData() {
        // Try to get from sessionStorage first (matching the key used in appointment.js)
        const sessionData = sessionStorage.getItem('appointment_data');
        if (sessionData) {
            try {
                const data = JSON.parse(sessionData);
                console.log('Loaded appointment data from session:', data);
                
                // Convert date to server format
                if (data.date) {
                    data.appointment_date = convertDateForServer(data.date);
                    data.appointment_time = data.time;
                    console.log('Converted date from', data.date, 'to', data.appointment_date);
                }
                
                return data;
            } catch (error) {
                console.error('Error parsing appointment data from session:', error);
            }
        }

        // Fallback to reading from DOM
        const domData = {
            doctor_name: document.getElementById('doctor-name')?.textContent || 'Unknown Doctor',
            service_type: document.getElementById('service-type')?.textContent || 'House Visit',
            date: document.getElementById('appointment-date')?.textContent || new Date().toLocaleDateString(),
            time: document.getElementById('appointment-time')?.textContent || '8:00 AM — 8:30 AM',
            location: document.getElementById('appointment-location')?.textContent || '',
            total_fee: parseFloat(document.getElementById('total-fee')?.textContent?.replace('$', '') || '300')
        };
        
        // Convert date to server format
        domData.appointment_date = convertDateForServer(domData.date);
        domData.appointment_time = domData.time;
        
        console.log('Using DOM data as fallback:', domData);
        return domData;
    }

    // Toggle add card form
    function attachAddCardButtonHandler() {
        const addBtn = document.querySelector('#addNewCardBtn');
        if (addBtn) {
            console.log('Attaching Add New Card button handler');
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Add New Card button clicked');
                toggleCardForm();
            });
        } else {
            console.error('Add New Card button not found with ID: addNewCardBtn');
        }
    }

    // Attach add card button handler
    attachAddCardButtonHandler();

    // Payment method selection
    function attachPaymentMethodHandlers() {
        const paymentInputs = document.querySelectorAll('.payment-label input[type="radio"]');
        console.log(`Found ${paymentInputs.length} payment method options`);
        
        paymentInputs.forEach((option, index) => {
            console.log(`Payment option ${index}: ${option.value}`);
            option.addEventListener('change', function() {
                selectedPaymentMethod = this.value;
                console.log(`Selected payment method: ${selectedPaymentMethod}`);
                
                // Hide card forms when selecting existing payment methods
                const cardForm = document.querySelector('#newCardForm');
                const legacyForm = document.querySelector('#legacyCardForm');
                
                if (cardForm && !cardForm.classList.contains('hidden')) {
                    cardForm.classList.add('hidden');
                    console.log('Hiding new card form due to payment method selection');
                }
                if (legacyForm && !legacyForm.classList.contains('hidden')) {
                    legacyForm.classList.add('hidden');
                    console.log('Hiding legacy card form due to payment method selection');
                }
            });
        });
    }

    // Attach payment method handlers
    attachPaymentMethodHandlers();

    /**
     * Toggle card form visibility
     */
    function toggleCardForm() {
        const cardForm = document.querySelector('#newCardForm');
        if (cardForm) {
            // Check the computed style to get the actual visibility
            const computedStyle = window.getComputedStyle(cardForm);
            const isDisplayHidden = computedStyle.display === 'none';
            const hasHiddenClass = cardForm.classList.contains('hidden');
            
            console.log(`Card form has hidden class: ${hasHiddenClass}`);
            console.log(`Card form display style: ${computedStyle.display}`);
            
            // If the form is hidden (either by class or display style), show it
            if (hasHiddenClass || isDisplayHidden) {
                cardForm.classList.remove('hidden');
                cardForm.style.display = 'block';
                console.log('Showing new card form');
                
                // Ensure card element is mounted
                const cardElement = document.getElementById('card-element');
                if (cardElement) {
                    // Check if card element is already mounted
                    if (!cardElement.hasChildNodes() || cardElement.children.length === 0) {
                        try {
                            card.mount('#card-element');
                            console.log('Card element mounted successfully');
                        } catch (error) {
                            console.error('Error mounting card element:', error);
                            // If mount fails, try to unmount first then mount again
                            try {
                                card.unmount();
                                setTimeout(() => {
                                    card.mount('#card-element');
                                    console.log('Card element re-mounted successfully');
                                }, 100);
                            } catch (remountError) {
                                console.error('Error re-mounting card element:', remountError);
                            }
                        }
                    } else {
                        console.log('Card element already mounted');
                    }
                }
                
                // Focus on card holder name input
                const cardHolderName = document.getElementById('cardHolderName');
                if (cardHolderName) {
                    setTimeout(() => cardHolderName.focus(), 200);
                }
            } else {
                cardForm.classList.add('hidden');
                cardForm.style.display = 'none';
                console.log('Hiding new card form');
                clearCardErrors();
            }
        } else {
            console.error('New card form not found with ID: newCardForm');
        }
    }

    /**
     * Clear card validation errors
     */
    function clearCardErrors() {
        const errorElement = document.getElementById('card-errors');
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }

    // Initialize appointment data from session storage if available
    function loadAppointmentData() {
        const appointmentData = getAppointmentData();
        if (appointmentData) {
            console.log('Loading appointment data into page:', appointmentData);
            
            // Update DOM elements with appointment data
            const elements = {
                'doctor-name': appointmentData.doctor_name || 'Dr. Not Available',
                'service-type': appointmentData.service_type || 'House Visit',
                'appointment-date': appointmentData.date || new Date().toLocaleDateString(),
                'appointment-time': appointmentData.time || '8:00 AM — 8:30 AM',
                'appointment-location': appointmentData.location || 'Not specified',
                'total-fee': `$${parseFloat(appointmentData.total_fee || 300).toFixed(2)}`
            };

            Object.entries(elements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value;
                    console.log(`Updated ${id} with: ${value}`);
                }
            });

            // Hide location row if service is not house visit
            const locationRow = document.getElementById('location-row');
            if (locationRow && appointmentData.service_type !== 'House Visit') {
                locationRow.style.display = 'none';
            }
        }
    }

    // Load appointment data on page load
    loadAppointmentData();

    // Ensure new card form is properly hidden initially
    function ensureFormInitialState() {
        const cardForm = document.querySelector('#newCardForm');
        if (cardForm) {
            cardForm.classList.add('hidden');
            cardForm.style.display = 'none';
            console.log('Ensured new card form is hidden initially');
        }
    }

    // Ensure proper initial state
    ensureFormInitialState();

    // Set Stripe publishable key from backend
    if (window.stripePublishableKey) {
        console.log('Stripe initialized with key:', window.stripePublishableKey.substring(0, 10) + '...');
    } else {
        console.warn('Stripe publishable key not found');
    }

    // Final initialization check
    function performInitializationCheck() {
        console.log('=== Payment Page Initialization Check ===');
        console.log('✓ Stripe available:', typeof Stripe !== 'undefined');
        console.log('✓ Publishable key set:', !!window.stripePublishableKey);
        console.log('✓ Add New Card button:', !!document.querySelector('#addNewCardBtn'));
        console.log('✓ Pay Now button:', !!document.querySelector('#payNowBtn'));
        console.log('✓ Cancel button:', !!document.querySelector('#cancelPaymentBtn'));
        console.log('✓ New card form:', !!document.querySelector('#newCardForm'));
        console.log('✓ Card element container:', !!document.getElementById('card-element'));
        console.log('✓ Payment options count:', document.querySelectorAll('.payment-label input[type="radio"]').length);
        
        // Check if appointment data is available
        const sessionData = sessionStorage.getItem('appointment_data');
        console.log('✓ Session appointment data:', !!sessionData);
        if (sessionData) {
            try {
                const data = JSON.parse(sessionData);
                console.log('  - Doctor:', data.doctor_name);
                console.log('  - Service:', data.service_type);
                console.log('  - Fee:', data.total_fee);
                console.log('  - Original date:', data.date);
                console.log('  - Converted date:', data.appointment_date || convertDateForServer(data.date));
            } catch (e) {
                console.error('  - Error parsing session data:', e);
            }
        }
        console.log('=========================================');
    }

    // Run initialization check
    setTimeout(performInitializationCheck, 100);

    // Add smooth animations for interactive elements
    const interactiveElements = document.querySelectorAll('.payment-label, button, input');
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', () => {
            element.style.transform = 'translateY(-2px)';
        });

        element.addEventListener('mouseleave', () => {
            element.style.transform = 'translateY(0)';
        });
    });

    // Handle card type detection and logo display
    function detectCardType(number) {
        const patterns = {
            visa: /^4/,
            mastercard: /^5[1-5]/,
            discover: /^6(?:011|5)/,
            maestro: /^(5018|5020|5038|6304|6759|676[1-3])/
        };

        for (const [type, pattern] of Object.entries(patterns)) {
            if (pattern.test(number)) {
                return type;
            }
        }
        return 'unknown';
    }

    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            const cardNumber = this.value.replace(/\D/g, '');
            const cardType = detectCardType(cardNumber);
            
            // You could update the UI to show the detected card type
            document.querySelectorAll('.card-types img').forEach(img => {
                img.style.opacity = img.alt.toLowerCase().includes(cardType) ? '1' : '0.3';
            });
        });
    }
});

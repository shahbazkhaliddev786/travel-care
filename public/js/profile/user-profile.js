document.addEventListener('DOMContentLoaded', function() {
    // Alert auto-dismiss
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 3000);
    });

    // Get form elements
    const paymentForm = document.getElementById('payment-form');
    const cardNumberInput = document.getElementById('cardNumber');
    const cardHolderInput = document.getElementById('cardHolder');
    const expiryMonthInput = document.querySelector('input[name="expiry_month"]');
    const expiryYearInput = document.querySelector('input[name="expiry_year"]');
    const cvvInput = document.querySelector('input[name="cvv"]');

    // Disable browser validation
    if (paymentForm) {
        paymentForm.setAttribute('novalidate', 'true');
    }

    // Card number formatting and validation
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/g, '');
            let formattedValue = value.replace(/(.{4})/g, '$1 ');
            if (formattedValue.endsWith(' ')) {
                formattedValue = formattedValue.slice(0, -1);
            }
            e.target.value = formattedValue;
            
            // Remove error styling on input
            e.target.closest('.input-group').classList.remove('error-border');
            const errorMessage = e.target.closest('.field-box').querySelector('.error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    }

    // Card holder validation (letters and spaces only)
    if (cardHolderInput) {
        cardHolderInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
            
            // Remove error styling on input
            e.target.closest('.input-group').classList.remove('error-border');
            const errorMessage = e.target.closest('.field-box').querySelector('.error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    }

    // Expiry month validation
    if (expiryMonthInput) {
        expiryMonthInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length >= 2) {
                const month = parseInt(value);
                if (month < 1 || month > 12) {
                    value = '';
                }
            }
            e.target.value = value;
            
            // Remove error styling on input
            e.target.closest('.input-group').classList.remove('error-border');
            const errorMessage = e.target.closest('.expire-date').querySelector('.error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    }

    // Expiry year validation
    if (expiryYearInput) {
        expiryYearInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            // Remove error styling on input
            e.target.closest('.input-group').classList.remove('error-border');
            const errorMessage = e.target.closest('.expire-date').querySelector('.error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    }

    // CVV validation
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            // Remove error styling on input
            e.target.closest('.input-group').classList.remove('error-border');
            const errorMessage = e.target.closest('.field-box').querySelector('.error-message');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    }

    // Form submission validation (basic client-side validation)
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            // Let server-side validation handle the detailed validation
            // Only prevent obviously empty submissions
            let hasEmptyFields = false;
            
            if (cardNumberInput && !cardNumberInput.value.trim()) {
                hasEmptyFields = true;
            }
            if (cardHolderInput && !cardHolderInput.value.trim()) {
                hasEmptyFields = true;
            }
            if (expiryMonthInput && !expiryMonthInput.value.trim()) {
                hasEmptyFields = true;
            }
            if (expiryYearInput && !expiryYearInput.value.trim()) {
                hasEmptyFields = true;
            }
            if (cvvInput && !cvvInput.value.trim()) {
                hasEmptyFields = true;
            }

            // Only prevent submission if fields are completely empty
            // Let server handle detailed validation
            if (hasEmptyFields) {
                e.preventDefault();
                alert('Please fill in all payment fields.');
            }
        });
    }
});
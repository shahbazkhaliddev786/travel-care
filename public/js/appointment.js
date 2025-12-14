document.addEventListener('DOMContentLoaded', function() {
    // Date Selection
    const dateItems = document.querySelectorAll('.date-item');
    const dateContainer = document.querySelector('.dates');
    const prevButton = document.querySelector('.date-nav.prev');
    const nextButton = document.querySelector('.date-nav.next');

    // Scroll dates
    let scrollAmount = 0;
    const scrollStep = 200;

    nextButton.addEventListener('click', () => {
        scrollAmount += scrollStep;
        dateContainer.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });

    prevButton.addEventListener('click', () => {
        scrollAmount = Math.max(0, scrollAmount - scrollStep);
        dateContainer.scrollTo({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });

    // Date selection
    dateItems.forEach(item => {
        item.addEventListener('click', () => {
            dateItems.forEach(d => d.classList.remove('active'));
            item.classList.add('active');
        });
    });

    // Time slot selection
    const timeSlots = document.querySelectorAll('.time-slot');
    timeSlots.forEach(slot => {
        slot.addEventListener('click', () => {
            timeSlots.forEach(s => s.classList.remove('active'));
            slot.classList.add('active');
        });
    });

    // Fee option selection
    const feeItems = document.querySelectorAll('.fee-item');
    feeItems.forEach(item => {
        item.addEventListener('click', () => {
            feeItems.forEach(f => f.classList.remove('active'));
            item.classList.add('active');
        });
    });

    // Address input animation
    const addressInput = document.querySelector('.address-input');
    addressInput.addEventListener('focus', () => {
        addressInput.parentElement.classList.add('focused');
    });

    addressInput.addEventListener('blur', () => {
        addressInput.parentElement.classList.remove('focused');
    });

    // Button actions
    const cancelButton = document.querySelector('.btn-cancel');
    const paymentButton = document.querySelector('.btn-payment');

    cancelButton.addEventListener('click', () => {
        // Add confirmation dialog
        if(confirm('Are you sure you want to cancel the appointment booking?')) {
            window.location.href = '/pages/doctor-profile.html';
        }
    });

    paymentButton.addEventListener('click', () => {
        // Validate selections
        const selectedDate = document.querySelector('.date-item.active');
        const selectedTime = document.querySelector('.time-slot.active');
        const selectedFee = document.querySelector('.fee-item.active');
        const address = addressInput.value.trim();

        if(!selectedDate || !selectedTime || !selectedFee) {
            alert('Please select a date, time, and service type.');
            return;
        }

        const serviceType = selectedFee.querySelector('.fee-details h4').textContent;
        
        if(serviceType === 'House Visit' && !address) {
            alert('Please enter your visit address.');
            addressInput.focus();
            return;
        }

        // Get appointment data
        const doctorName = document.querySelector('.profile-card h1')?.textContent || 'Dr. Not Available';
        
        const appointmentData = {
            doctor_id: window.location.pathname.split('/').pop(), // Extract doctor ID from URL
            doctor_name: doctorName,
            service_type: serviceType,
            date: selectedDate.querySelector('.day').textContent,
            time: selectedTime.textContent,
            location: serviceType === 'House Visit' ? address : null,
            total_fee: selectedFee.querySelector('.fee-amount').textContent.replace('$', '').replace(',', '').match(/\d+\.?\d*/)?.[0] || '0'
        };

        // Store appointment data in session storage and navigate to payment
        sessionStorage.setItem('appointment_data', JSON.stringify(appointmentData));
        
        // Navigate to payment page using GET request
        window.location.href = '/payment';
    });

    // Add smooth scroll for time slots section
    const timeSection = document.querySelector('.time-slots h3');
    timeSection.addEventListener('click', () => {
        const timeGrid = document.querySelector('.time-grid');
        timeGrid.scrollIntoView({ behavior: 'smooth' });
    });

    // Add hover animations for interactive elements
    const interactiveElements = document.querySelectorAll('.date-item, .time-slot, .fee-item, button');
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', () => {
            element.style.transform = 'translateY(-2px)';
        });

        element.addEventListener('mouseleave', () => {
            element.style.transform = 'translateY(0)';
        });
    });
});

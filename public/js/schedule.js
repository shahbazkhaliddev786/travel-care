// Schedule Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Calendar functionality
    const calendarTitle = document.getElementById('calendarTitle');
    const calendarDays = document.getElementById('calendarDays');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    
    let currentDate = new Date();
    const today = new Date();
    
    // Days with appointments (from server data)
    const appointmentDays = window.calendarData ? window.calendarData.highlighted_days : [];
    
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    function generateCalendar(year, month) {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const firstDayOfWeek = (firstDay.getDay() + 6) % 7; // Monday = 0
        const daysInMonth = lastDay.getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();
        
        calendarTitle.textContent = `${monthNames[month]}, ${year}`;
        calendarDays.innerHTML = '';
        
        // Previous month's trailing days
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const dayElement = createDayElement(daysInPrevMonth - i, 'other-month');
            calendarDays.appendChild(dayElement);
        }
        
        // Current month's days
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = createDayElement(day, 'current-month');
            
            // Highlight days with appointments (for current month)
            if (year === today.getFullYear() && month === today.getMonth() && appointmentDays.includes(day)) {
                dayElement.classList.add('highlighted');
            }
            
            // Mark today
            if (year === today.getFullYear() && 
                month === today.getMonth() && 
                day === today.getDate()) {
                dayElement.classList.add('today');
            }
            
            calendarDays.appendChild(dayElement);
        }
        
        // Next month's leading days
        const totalCells = 42; // 6 rows × 7 days
        const usedCells = firstDayOfWeek + daysInMonth;
        const remainingCells = totalCells - usedCells;
        
        for (let day = 1; day <= remainingCells && remainingCells < 7; day++) {
            const dayElement = createDayElement(day, 'other-month');
            calendarDays.appendChild(dayElement);
        }
    }
    
    function createDayElement(day, type) {
        const dayElement = document.createElement('div');
        dayElement.className = `calendar-day ${type}`;
        dayElement.textContent = day;
        
        dayElement.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.calendar-day.selected').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selection to clicked day (only for current month days)
            if (type === 'current-month') {
                dayElement.classList.add('selected');
                // Here you could add logic to filter appointments by selected date
            }
        });
        
        return dayElement;
    }
    
    function previousMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    }
    
    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    }
    
    // Event listeners
    prevMonthBtn.addEventListener('click', previousMonth);
    nextMonthBtn.addEventListener('click', nextMonth);
    
    // Initialize calendar
    generateCalendar(currentDate.getFullYear(), currentDate.getMonth());
    
    // Animate appointment items on scroll
    const appointmentItems = document.querySelectorAll('.appointment-item');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                entry.target.style.transition = 'all 0.3s ease';
                
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
            }
        });
    }, observerOptions);
    
    appointmentItems.forEach(item => {
        observer.observe(item);
    });
    
    // Add hover effects to appointment items
    appointmentItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // Modal functionality
    const modal = document.getElementById('appointmentModal');
    const closeModalBtn = document.getElementById('closeModal');
    const modalOverlay = document.querySelector('.modal-overlay');
    
    // Modal elements for data population
    const modalPatient = document.getElementById('modalPatient');
    const modalAge = document.getElementById('modalAge');
    const modalService = document.getElementById('modalService');
    const modalTime = document.getElementById('modalTime');
    const modalLocation = document.getElementById('modalLocation');
    const modalFee = document.getElementById('modalFee');
    
    // Add click event to all appointment items
    appointmentItems.forEach(item => {
        item.addEventListener('click', function() {
            // Get data from clicked appointment
            const patient = this.dataset.patient;
            const age = this.dataset.age;
            const service = this.dataset.service;
            const time = this.dataset.time;
            const location = this.dataset.location;
            const fee = this.dataset.fee;
            
            // Populate modal with data
            modalPatient.textContent = patient;
            modalAge.textContent = age;
            modalService.textContent = service;
            modalTime.textContent = time;
            modalLocation.textContent = location;
            modalFee.textContent = fee;
            
            // Show modal
            showModal();
        });
    });
    
    // Close modal functions
    closeModalBtn.addEventListener('click', hideModal);
    modalOverlay.addEventListener('click', hideModal);
    
    // Escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            hideModal();
        }
    });
    
    function showModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function hideModal() {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    
    // Button actions
    const startVideoCallBtn = document.getElementById('startVideoCall');
    const contactPatientBtn = document.getElementById('contactPatient');
    const contactDoctorBtn = document.getElementById('contactDoctor');
    
    if (startVideoCallBtn) {
        startVideoCallBtn.addEventListener('click', function() {
            // For customers, this button says "Contact Doctor" 
            // For doctors, this button says "Start A Video Call"
            if (window.userRole === 'doctor') {
                // Implement video call functionality for doctors
                console.log('Starting video call...');
            } else {
                // For customers, navigate to chats page
                window.location.href = '/chats';
            }
            hideModal();
        });
    }
    
    // Handle contact patient button (for doctors)
    if (contactPatientBtn) {
        contactPatientBtn.addEventListener('click', function() {
            // Get the patient ID from the current appointment data
            const patientName = modalPatient.textContent;
            
            // Navigate to chats page
            window.location.href = '/chats';
            hideModal();
        });
    }
    
    // Handle contact doctor button (for customers - this is "Write A Review")
    if (contactDoctorBtn) {
        contactDoctorBtn.addEventListener('click', function() {
            // This button is for writing a review, not contacting
            // Implement review functionality
            console.log('Writing a review...');
            hideModal();
        });
    }
}); 
document.addEventListener('DOMContentLoaded', function() {
    // Get all tab buttons
    const tabs = document.querySelectorAll('.tab');
    
    // Add click event listener to each tab
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
            if (tabId === 'reviews') {
                location.hash = '#reviews';
            } else {
                location.hash = '#services';
            }
        });
    });

    const hash = location.hash;
    if (hash === '#reviews') {
        const reviewsTab = document.querySelector('.tab[data-tab="reviews"]');
        if (reviewsTab) reviewsTab.click();
    }

    // Pagination functionality
    const prevButton = document.querySelector('.reviews-section .prev-page');
    const nextButton = document.querySelector('.reviews-section .next-page');
    const pageNumbers = document.querySelectorAll('.reviews-section .page-numbers a');

    // Add click event listeners to page numbers
    pageNumbers.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.href.includes('#reviews')) {
                e.preventDefault();
                location.href = this.href + '#reviews';
            }
        });
    });

    // Add click event listeners to prev/next buttons
    if (prevButton && nextButton) {
        prevButton.addEventListener('click', function(e) {
            if (this.tagName.toLowerCase() === 'a' && !this.href.includes('#reviews')) {
                e.preventDefault();
                location.href = this.href + '#reviews';
            }
        });

        nextButton.addEventListener('click', function(e) {
            if (this.tagName.toLowerCase() === 'a' && !this.href.includes('#reviews')) {
                e.preventDefault();
                location.href = this.href + '#reviews';
            }
        });
    }

    const popup = document.getElementById('servicePopup');
    const confirmBtn = document.getElementById('servicePopupConfirm');
    const cancelBtn = document.getElementById('servicePopupCancel');
    const popupName = document.getElementById('popupServiceName');
    const popupPrice = document.getElementById('popupServicePrice');
    let selectedService = { name: '', price: 0 };

    function openServicePopup(name, price) {
        selectedService = { name, price };
        popupName.textContent = name;
        popupPrice.textContent = `$${parseFloat(price).toFixed(0)}`;
        popup.classList.add('active');
    }

    function closeServicePopup() {
        popup.classList.remove('active');
    }

    document.querySelectorAll('.services-list .price-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.service-item');
            const name = item.querySelector('.service-info h3')?.textContent || 'Consultation';
            const priceText = btn.textContent.replace('$', '').replace(',', '');
            const price = priceText.match(/\d+\.?\d*/)?.[0] || '0';
            openServicePopup(name, price);
        });
    });

    cancelBtn?.addEventListener('click', () => {
        closeServicePopup();
    });

    popup?.addEventListener('click', (e) => {
        if (e.target === popup) {
            closeServicePopup();
        }
    });

    confirmBtn?.addEventListener('click', () => {
        const doctorName = document.querySelector('.profile-card h1')?.textContent || 'Dr. Not Available';
        const doctorId = window.location.pathname.split('/').pop();
        const appointmentData = {
            doctor_id: doctorId,
            doctor_name: doctorName,
            service_type: selectedService.name,
            total_fee: selectedService.price
        };
        sessionStorage.setItem('appointment_data', JSON.stringify(appointmentData));
        window.location.href = '/payment';
    });
});

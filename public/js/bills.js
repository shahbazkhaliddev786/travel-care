// Bills/Payments Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Payment items functionality
    const paymentItems = document.querySelectorAll('.payment-item');
    const invoiceElements = {
        number: document.getElementById('invoiceNumber'),
        date: document.getElementById('invoiceDate'),
        recipient: document.getElementById('recipientName'),
        service: document.getElementById('serviceName'),
        amount: document.getElementById('totalAmount'),
        charges: document.getElementById('platformCharges')
    };
    
    // Modal elements
    const invoiceModal = document.getElementById('invoiceModal');
    const closeModalBtn = document.getElementById('closeInvoiceModal');
    const modalElements = {
        number: document.getElementById('modalInvoiceNumber'),
        date: document.getElementById('modalInvoiceDate'),
        recipient: document.getElementById('modalRecipientName'),
        service: document.getElementById('modalServiceName'),
        amount: document.getElementById('modalTotalAmount'),
        charges: document.getElementById('modalPlatformCharges')
    };
    
    // Pagination functionality
    const paginationBtns = document.querySelectorAll('.pagination-btn');
    const prevBtn = document.querySelector('.pagination-btn.prev');
    const nextBtn = document.querySelector('.pagination-btn.next');
    
    // Add click event to all payment items
    paymentItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.payment-item.selected').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selection to clicked item
            this.classList.add('selected');
            
            // Get data from clicked payment
            const invoiceNumber = this.dataset.invoice;
            const date = this.dataset.date;
            const recipient = this.dataset.recipient;
            const service = this.dataset.service;
            const amount = this.dataset.amount;
            const charges = this.dataset.charges;
            
            // Update invoice details
            updateInvoiceDetails(invoiceNumber, date, recipient, service, amount, charges);
            
            // Show modal on tablet/mobile (below 1024px)
            if (window.innerWidth <= 1024) {
                showInvoiceModal(invoiceNumber, date, recipient, service, amount, charges);
            }
        });
    });
    
    function updateInvoiceDetails(invoice, date, recipient, service, amount, charges) {
        // Update invoice details with animation
        const detailRows = document.querySelectorAll('.detail-row');
        
        detailRows.forEach(row => {
            row.style.opacity = '0.7';
            row.style.transform = 'translateY(2px)';
        });
        
        setTimeout(() => {
            if (invoiceElements.number) invoiceElements.number.textContent = invoice;
            if (invoiceElements.date) invoiceElements.date.textContent = date;
            if (invoiceElements.recipient) invoiceElements.recipient.textContent = recipient;
            if (invoiceElements.service) invoiceElements.service.textContent = service;
            if (invoiceElements.amount) invoiceElements.amount.textContent = `$${amount}`;
            if (invoiceElements.charges) invoiceElements.charges.textContent = `$${charges}`;
            
            detailRows.forEach(row => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
                row.style.transition = 'all 0.3s ease';
            });
        }, 150);
    }
    
    // Modal functions
    function showInvoiceModal(invoice, date, recipient, service, amount, charges) {
        if (!invoiceModal) {
            console.error('Invoice modal not found');
            return;
        }
        
        // Update modal content
        if (modalElements.number) modalElements.number.textContent = invoice;
        if (modalElements.date) modalElements.date.textContent = date;
        if (modalElements.recipient) modalElements.recipient.textContent = recipient;
        if (modalElements.service) modalElements.service.textContent = service;
        if (modalElements.amount) modalElements.amount.textContent = `$${amount}`;
        if (modalElements.charges) modalElements.charges.textContent = `$${charges}`;
        
        // Show modal
        invoiceModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Add animation
        setTimeout(() => {
            invoiceModal.classList.add('show');
        }, 10);
    }
    
    function hideInvoiceModal() {
        if (!invoiceModal) {
            return;
        }
        
        invoiceModal.classList.remove('show');
        document.body.style.overflow = '';
        
        setTimeout(() => {
            invoiceModal.style.display = 'none';
        }, 300);
    }
    
    // Modal event listeners
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', hideInvoiceModal);
    }
    
    // Close modal when clicking outside
    if (invoiceModal) {
        invoiceModal.addEventListener('click', function(e) {
            if (e.target === invoiceModal) {
                hideInvoiceModal();
            }
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024 && invoiceModal.style.display === 'flex') {
            hideInvoiceModal();
        }
    });
    
    // Pagination functionality
    paginationBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.classList.contains('prev') || this.classList.contains('next')) {
                return; // Handle prev/next separately if needed
            }
            
            // Remove active class from all buttons
            paginationBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Here you would typically load the corresponding page data
            console.log('Loading page:', this.textContent);
        });
    });
    
    // Withdraw button functionality
    const withdrawBtn = document.querySelector('.withdraw-btn');
    withdrawBtn.addEventListener('click', function() {
        // Implement withdraw functionality
        console.log('Withdraw requested');
        // You can add a modal or redirect to withdraw page
    });
    
    // Animate payment items on scroll
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
    
    paymentItems.forEach(item => {
        observer.observe(item);
    });
    
    // Add hover effects to payment items
    paymentItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'translateX(4px)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('selected')) {
                this.style.transform = 'translateX(0)';
            }
        });
    });
    
    // Auto-select first payment item on load
    if (paymentItems.length > 0) {
        paymentItems[0].click();
    }
});
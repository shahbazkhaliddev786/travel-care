/**
 * Payment Popup Component
 * Handles success and failure payment popups with transaction details
 */
class PaymentPopup {
    constructor() {
        this.overlay = null;
        this.currentPopup = null;
        this.isVisible = false;
        this.autoCloseTimer = null;
        
        // Initialize on page load
        this.init();
    }

    /**
     * Initialize the popup system
     */
    init() {
        // Create overlay element if it doesn't exist
        this.createOverlay();
        
        // Bind event listeners
        this.bindEvents();
    }

    /**
     * Create the overlay element
     */
    createOverlay() {
        if (document.getElementById('payment-popup-overlay')) {
            this.overlay = document.getElementById('payment-popup-overlay');
            return;
        }

        this.overlay = document.createElement('div');
        this.overlay.id = 'payment-popup-overlay';
        this.overlay.className = 'payment-popup-overlay';
        this.overlay.innerHTML = `
            <div class="payment-popup" id="payment-popup">
                <button class="payment-popup-close" id="payment-popup-close">&times;</button>
                <div class="payment-popup-content" id="payment-popup-content">
                    <!-- Dynamic content will be inserted here -->
                </div>
            </div>
        `;
        
        document.body.appendChild(this.overlay);
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Close popup when clicking overlay
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.hide();
            }
        });

        // Close popup when clicking close button
        const closeBtn = document.getElementById('payment-popup-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                this.hide();
            });
        }

        // Close popup with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isVisible) {
                this.hide();
            }
        });
    }

    /**
     * Show success popup
     */
    showSuccess(options = {}) {
        const {
            transactionId = '',
            amount = '',
            doctorName = '',
            serviceType = '',
            appointmentDate = '',
            appointmentTime = '',
            location = '',
            message = 'Your payment has been processed successfully!',
            autoClose = true,
            autoCloseDelay = 5000
        } = options;

        const content = `
            <div class="payment-popup-header">
                <div class="payment-popup-icon success"></div>
                <h2 class="payment-popup-title">Payment Successful!</h2>
                <p class="payment-popup-message">${message}</p>
            </div>
            
            ${transactionId ? `
                <div class="payment-popup-transaction-id">
                    Transaction ID: ${transactionId}
                </div>
            ` : ''}
            
            <div class="payment-popup-details">
                ${amount ? `
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Amount Paid:</span>
                        <span class="payment-popup-detail-value">$${parseFloat(amount).toFixed(2)}</span>
                    </div>
                ` : ''}
                
                ${doctorName ? `
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Doctor:</span>
                        <span class="payment-popup-detail-value">${doctorName}</span>
                    </div>
                ` : ''}
                
                ${serviceType ? `
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Service:</span>
                        <span class="payment-popup-detail-value">${serviceType}</span>
                    </div>
                ` : ''}
                
                ${appointmentDate ? `
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Date:</span>
                        <span class="payment-popup-detail-value">${appointmentDate}</span>
                    </div>
                ` : ''}
                
                ${appointmentTime ? `
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Time:</span>
                        <span class="payment-popup-detail-value">${appointmentTime}</span>
                    </div>
                ` : ''}
                
                ${location ? `
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Location:</span>
                        <span class="payment-popup-detail-value">${location}</span>
                    </div>
                ` : ''}
            </div>
            
            <div class="payment-popup-actions">
                <button class="payment-popup-btn payment-popup-btn-primary" onclick="paymentPopup.redirectToHome()">
                    <i class="fas fa-home"></i> Go to Home
                </button>
                <button class="payment-popup-btn payment-popup-btn-secondary" onclick="paymentPopup.printReceipt()">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
                <button class="payment-popup-btn payment-popup-btn-close" onclick="paymentPopup.hide()">
                    Close
                </button>
            </div>
        `;

        this.show(content);

        // Auto-close after delay
        if (autoClose) {
            this.autoCloseTimer = setTimeout(() => {
                this.hide();
                this.redirectToHome();
            }, autoCloseDelay);
        }
    }

    /**
     * Show failure popup
     */
    showFailure(options = {}) {
        const {
            errorMessage = 'Payment failed. Please try again.',
            errorCode = '',
            amount = '',
            doctorName = '',
            serviceType = '',
            retryCallback = null,
            autoClose = false
        } = options;

        const content = `
            <div class="payment-popup-header">
                <div class="payment-popup-icon error"></div>
                <h2 class="payment-popup-title">Payment Failed</h2>
                <p class="payment-popup-message">${errorMessage}</p>
            </div>
            
            ${errorCode ? `
                <div class="payment-popup-details">
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Error Code:</span>
                        <span class="payment-popup-detail-value">${errorCode}</span>
                    </div>
                </div>
            ` : ''}
            
            <div class="payment-popup-details">
                ${amount ? `
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Amount:</span>
                        <span class="payment-popup-detail-value">$${parseFloat(amount).toFixed(2)}</span>
                    </div>
                ` : ''}
                
                ${doctorName ? `
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Doctor:</span>
                        <span class="payment-popup-detail-value">${doctorName}</span>
                    </div>
                ` : ''}
                
                ${serviceType ? `
                    <div class="payment-popup-detail-row">
                        <span class="payment-popup-detail-label">Service:</span>
                        <span class="payment-popup-detail-value">${serviceType}</span>
                    </div>
                ` : ''}
            </div>
            
            <div class="payment-popup-actions">
                ${retryCallback ? `
                    <button class="payment-popup-btn payment-popup-btn-primary" onclick="paymentPopup.retryPayment()">
                        <i class="fas fa-redo"></i> Try Again
                    </button>
                ` : ''}
                <button class="payment-popup-btn payment-popup-btn-secondary" onclick="paymentPopup.contactSupport()">
                    <i class="fas fa-phone"></i> Contact Support
                </button>
                <button class="payment-popup-btn payment-popup-btn-close" onclick="paymentPopup.hide()">
                    Close
                </button>
            </div>
        `;

        this.show(content);
        this.retryCallback = retryCallback;
        
        // Auto-close after delay if specified
        if (autoClose) {
            this.autoCloseTimer = setTimeout(() => {
                this.hide();
            }, 10000); // 10 seconds for failure
        }
    }

    /**
     * Show loading popup
     */
    showLoading(message = 'Processing payment...') {
        const content = `
            <div class="payment-popup-header">
                <div class="payment-popup-loading">
                    <div class="payment-popup-spinner"></div>
                    <div class="payment-popup-loading-text">${message}</div>
                </div>
            </div>
        `;

        this.show(content);
    }

    /**
     * Show popup with custom content
     */
    show(content) {
        if (this.autoCloseTimer) {
            clearTimeout(this.autoCloseTimer);
            this.autoCloseTimer = null;
        }

        const contentElement = document.getElementById('payment-popup-content');
        if (contentElement) {
            contentElement.innerHTML = content;
        }

        this.overlay.classList.add('show');
        this.isVisible = true;
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Focus management for accessibility
        const firstFocusable = this.overlay.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) {
            firstFocusable.focus();
        }
    }

    /**
     * Hide popup
     */
    hide() {
        if (this.autoCloseTimer) {
            clearTimeout(this.autoCloseTimer);
            this.autoCloseTimer = null;
        }

        this.overlay.classList.remove('show');
        this.isVisible = false;
        
        // Restore body scroll
        document.body.style.overflow = '';
        
        // Clear content after animation
        setTimeout(() => {
            const contentElement = document.getElementById('payment-popup-content');
            if (contentElement) {
                contentElement.innerHTML = '';
            }
        }, 300);
    }

    /**
     * Redirect to home page
     */
    redirectToHome() {
        window.location.href = '/home';
    }

    /**
     * Print receipt
     */
    printReceipt() {
        window.print();
    }

    /**
     * Retry payment
     */
    retryPayment() {
        this.hide();
        if (this.retryCallback && typeof this.retryCallback === 'function') {
            this.retryCallback();
        }
    }

    /**
     * Contact support
     */
    contactSupport() {
        window.location.href = '/support';
    }

    /**
     * Destroy popup instance
     */
    destroy() {
        if (this.overlay) {
            this.overlay.remove();
        }
        if (this.autoCloseTimer) {
            clearTimeout(this.autoCloseTimer);
        }
    }
}

// Create global instance
let paymentPopup;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    paymentPopup = new PaymentPopup();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PaymentPopup;
}
// Admin Panel JavaScript

// Sidebar toggle functions
function openSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.add('show');
    overlay.classList.add('show');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.remove('show');
    overlay.classList.remove('show');
}

// Close sidebar when clicking nav links on mobile/tablet
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 991.98) {
                closeSidebar();
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 991.98) {
            closeSidebar();
        }
    });
});

// Custom dropdown functionality - only for non-Bootstrap dropdowns
function initDropdowns() {
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle:not([data-bs-toggle="dropdown"])');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdown = this.closest('.dropdown');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            // Close all other custom dropdowns (not Bootstrap ones)
            document.querySelectorAll('.dropdown-menu.show').forEach(otherMenu => {
                const otherToggle = otherMenu.closest('.dropdown').querySelector('.dropdown-toggle');
                if (otherMenu !== menu && !otherToggle.hasAttribute('data-bs-toggle')) {
                    otherMenu.classList.remove('show');
                    otherToggle.setAttribute('aria-expanded', 'false');
                }
            });
            
            // Toggle current dropdown
            const isOpen = menu.classList.contains('show');
            if (isOpen) {
                menu.classList.remove('show');
                this.setAttribute('aria-expanded', 'false');
            } else {
                menu.classList.add('show');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
    
    // Close custom dropdowns when clicking outside (but not Bootstrap ones)
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                const toggle = menu.closest('.dropdown').querySelector('.dropdown-toggle');
                if (!toggle.hasAttribute('data-bs-toggle')) {
                    menu.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
}

// Custom alert close functionality
function initAlerts() {
    const closeButtons = document.querySelectorAll('.btn-close');
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 150);
            }
        });
    });
}

// Initialize all custom functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdowns
    initDropdowns();
    
    // Initialize alert close buttons
    initAlerts();
    
    // Auto-hide alerts after 4 seconds (only if they don't have a custom handler)
    const alerts = document.querySelectorAll('.alert:not([data-custom-handler])');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 150);
            }
        }, 4000);
    });
});

// Add loading state to buttons when clicked (except on profile page and modals)
document.addEventListener('click', function(e) {
    // Skip if we're on the profile page - let profile page handle its own buttons
    if (window.location.pathname.includes('/admin/profile')) {
        return;
    }
    
    // Skip if the button is inside a modal
    if (e.target.closest('.modal')) {
        return;
    }
    
    if (e.target.matches('.btn-primary') || e.target.closest('.btn-primary')) {
        const btn = e.target.matches('.btn-primary') ? e.target : e.target.closest('.btn-primary');
        if (btn.type === 'submit') {
            // For submit buttons, show loading state after a short delay to allow form submission
            setTimeout(() => {
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<span class="loading"></span> Loading...';
                btn.disabled = true;
                
                // Reset after 3 seconds (in case form doesn't submit)
                setTimeout(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }, 3000);
            }, 100);
        } else if (btn.onclick) {
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<span class="loading"></span> Loading...';
            btn.disabled = true;
            
            // Reset after 3 seconds (in case action doesn't complete)
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }, 3000);
        }
    }
});

// Handle responsive table headers
function handleResponsiveTable() {
    const tables = document.querySelectorAll('.table-responsive');
    tables.forEach(table => {
        if (window.innerWidth <= 767.98) {
            table.style.overflowX = 'auto';
        } else {
            table.style.overflowX = 'visible';
        }
    });
}

// Run on load and resize
window.addEventListener('load', handleResponsiveTable);
window.addEventListener('resize', handleResponsiveTable);
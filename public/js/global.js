document.addEventListener("DOMContentLoaded", function () {
    // Set up CSRF token for AJAX requests
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        const csrfToken = token.getAttribute('content');
        
        // Set up CSRF token for jQuery AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        // For Axios
        if (window.axios) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        }
        
        // Make CSRF token available for fetch API
        window.CSRF_TOKEN = csrfToken;
    }
    
    // Back Button Handling
    const prevButton = document.getElementById('prevButton');
    if (prevButton) {
        prevButton.addEventListener('click', function (event) {
            event.preventDefault();
            window.history.back();
        });
    }

    // Animation Script
    function applyFadeInAnimation(parentSelector) {
        if (!parentSelector) return;

        const children = parentSelector.children; 
        Array.from(children).forEach((child, index) => { // Convert to array for forEach
            child.style.animationDelay = `${index * 0.2}s`; 
            child.classList.add("fade-in"); 
        });
    }

    // Apply animation to all forms or containers
    document.querySelectorAll(".animated-container").forEach(container => {
        applyFadeInAnimation(container);
    });
    
});

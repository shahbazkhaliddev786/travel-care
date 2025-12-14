document.addEventListener('DOMContentLoaded', function() {
    // Handle gender selection
    const genderButtons = document.querySelectorAll('.gender');
    const genderInput = document.getElementById('gender');
    
    if (genderButtons.length > 0 && genderInput) {
        genderButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                // Remove active class from all buttons
                genderButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                // Update hidden input value
                genderInput.value = this.getAttribute('data-value');
            });
        });
    }
    
    // Add More functionality for Chronic Pathologies, Allergies, Chronic Medication
    document.querySelectorAll('.dotted-btn').forEach(addButton => {
        addButton.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Find parent label-input container
            const labelInputContainer = this.closest('.label-input');
            const firstInput = labelInputContainer.querySelector('input');
            
            if (!firstInput) return;
            
            // Create new input field wrapper
            const newInputDiv = document.createElement('div');
            newInputDiv.className = 'input-group';
            
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.name = firstInput.name; // Keep the array notation
            newInput.placeholder = firstInput.placeholder;
            newInput.required = false; // Additional fields are not required
            
            // Create remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-field-btn';
            removeBtn.textContent = '✕';
            
            // Add remove functionality
            removeBtn.addEventListener('click', function() {
                newInputDiv.remove();
            });
            
            // Append elements
            newInputDiv.appendChild(newInput);
            newInputDiv.appendChild(removeBtn);
            
            // Insert the new input group before the Add More button
            labelInputContainer.insertBefore(newInputDiv, this);
        });
    });
    
    // Handle profile photo upload
    const profilePhotoInput = document.getElementById('profile_photo_input');
    if (profilePhotoInput) {
        profilePhotoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Show loading indicator or disable form if needed
                document.getElementById('photo-upload-form').submit();
            }
        });
    }
});

// Add event listeners for existing remove buttons
document.addEventListener('DOMContentLoaded', function() {
    // Handle existing remove buttons
    document.querySelectorAll('.remove-field-btn').forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.remove();
        });
    });
});

// Show success messages
const urlParams = new URLSearchParams(window.location.search);
const successMessage = urlParams.get('success');
if (successMessage) {
    // Create and show a success message element
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success';
    alertDiv.textContent = successMessage;
    
    // Insert at the top of the content area
    const container = document.querySelector('.container');
    if (container) {
        container.prepend(alertDiv);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}
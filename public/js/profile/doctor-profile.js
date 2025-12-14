document.addEventListener('DOMContentLoaded', function() {
    // Profile Image Upload Preview
    const profileImageInput = document.getElementById('profile_image');
    const profilePreview = document.getElementById('profile-preview');
    const deletePhotoBtn = document.getElementById('delete-photo-btn');
    const deleteProfileImageField = document.getElementById('delete_profile_image');
    
    if (profileImageInput && profilePreview) {
        profileImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                    // Reset delete flag when new image is selected
                    if (deleteProfileImageField) {
                        deleteProfileImageField.value = '0';
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Handle profile image deletion
    if (deletePhotoBtn && deleteProfileImageField) {
        deletePhotoBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete your profile photo?')) {
                // Set default image
                profilePreview.src = '/assets/icons/profile.svg';
                // Set delete flag for form submission
                deleteProfileImageField.value = '1';
                // Clear any selected file
                if (profileImageInput) {
                    profileImageInput.value = '';
                }
            }
        });
    }

    // Toggle checkboxes
    document.querySelectorAll('.custom-checkbox').forEach(checkbox => {
        checkbox.addEventListener('click', function() {
            this.classList.toggle('checked');
            const day = this.getAttribute('data-day');
            const input = this.parentElement.querySelector(`input[value="${day}"]`);
            
            if (this.classList.contains('checked')) {
                this.innerHTML = '<i class="fas fa-check"></i>';
                input.disabled = false;
            } else {
                this.innerHTML = '';
                input.disabled = true;
            }
            
            // Auto-submit the form when a day is toggled
            document.getElementById('working-hours-form').submit();
        });
    });

    // Time picker functionality
    document.querySelectorAll('.time-arrow').forEach(arrow => {
        arrow.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const timeSpan = document.getElementById(`${targetId}_display`);
            const timeInput = document.getElementById(targetId);
            const currentTime = timeSpan.textContent;
            const isAM = currentTime.includes('AM');
            const timeParts = currentTime.replace(/ (AM|PM)/, '').split(':');
            let hours = parseInt(timeParts[0]);
            let minutes = parseInt(timeParts[1] || '0');
            
            if (this.classList.contains('fa-chevron-left')) {
                // Decrease time
                if (minutes === 0) {
                    hours = (hours - 1 + 12) % 12 || 12;
                    minutes = 30;
                } else {
                    minutes = 0;
                }
            } else {
                // Increase time
                if (minutes === 30) {
                    hours = (hours + 1) % 12 || 12;
                    minutes = 0;
                } else {
                    minutes = 30;
                }
            }
            
            const period = isAM ? 'AM' : 'PM';
            const newTime = `${hours}:${minutes === 0 ? '00' : minutes} ${period}`;
            timeSpan.textContent = newTime;
            
            // Convert to 24-hour format for the hidden input
            let hours24 = hours;
            if (period === 'PM' && hours !== 12) hours24 += 12;
            if (period === 'AM' && hours === 12) hours24 = 0;
            
            timeInput.value = `${hours24.toString().padStart(2, '0')}:${minutes === 0 ? '00' : minutes}:00`;
            
            // Auto-submit the form when time is changed
            document.getElementById('working-hours-form').submit();
        });
    });

    // Auto-submit fees form when inputs change
    document.querySelectorAll('#fees-form input').forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('fees-form').submit();
        });
    });

    // Inline edit functionality for services
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const serviceId = this.getAttribute('data-id');
            
            // Hide all other edit forms first
            document.querySelectorAll('.service-edit-form').forEach(form => {
                form.style.display = 'none';
            });
            
            // Show the edit form for this service
            const editForm = document.getElementById(`edit-form-${serviceId}`);
            if (editForm) {
                editForm.style.display = 'block';
            }
        });
    });

    // Add service button
    const addServiceButton = document.querySelector('.add-service');
    if (addServiceButton) {
        addServiceButton.addEventListener('click', function() {
            // Hide all service edit forms
            document.querySelectorAll('.service-edit-form').forEach(form => {
                if (form.id !== 'add-service-form') {
                    form.style.display = 'none';
                }
            });
            
            // Toggle the add service form
            const addForm = document.getElementById('add-service-form');
            if (addForm) {
                addForm.style.display = addForm.style.display === 'none' ? 'block' : 'none';
            }
        });
    }

    // Profile edit modal functionality
    const editProfileIcon = document.querySelector('.edit-icon');
    const editProfileModal = document.getElementById('editProfileModal');
    const closeEditProfileModalBtn = editProfileModal ? editProfileModal.querySelector('.close-modal') : null;
    
    if (editProfileIcon && editProfileModal) {
        // Open modal when edit icon is clicked
        editProfileIcon.addEventListener('click', function() {
            editProfileModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling behind modal
        });
        
        // Close modal when X is clicked
        if (closeEditProfileModalBtn) {
            closeEditProfileModalBtn.addEventListener('click', function() {
                editProfileModal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Re-enable scrolling
            });
        }
        
        // Close modal when clicking outside the modal content
        window.addEventListener('click', function(event) {
            if (event.target === editProfileModal) {
                editProfileModal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Re-enable scrolling
            }
        });
    }

    // Handle edit profile form submission
    const editProfileForm = document.getElementById('edit-profile-form');
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function(e) {
            // Add a loading state to prevent multiple submissions
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }
        });
    }

    // Payment methods modal functionality
    const paymentInfoBtn = document.querySelector('.payment-info');
    const paymentMethodsModal = document.getElementById('paymentMethodsModal');
    const closePaymentModalBtn = paymentMethodsModal ? paymentMethodsModal.querySelector('.close-modal') : null;
    
    if (paymentInfoBtn && paymentMethodsModal) {
        // Open modal when payment info is clicked
        paymentInfoBtn.addEventListener('click', function() {
            paymentMethodsModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling behind modal
        });
        
        // Close modal when X is clicked
        if (closePaymentModalBtn) {
            closePaymentModalBtn.addEventListener('click', function() {
                paymentMethodsModal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Re-enable scrolling
            });
        }
        
        // Close modal when clicking outside the modal content
        window.addEventListener('click', function(event) {
            if (event.target === paymentMethodsModal) {
                paymentMethodsModal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Re-enable scrolling
            }
        });
    }

    // Add card modal functionality
    const addCardModal = document.getElementById('addCardModal');
    const addBankCardBtn = document.getElementById('addBankCardBtn');
    const closeAddCardModalBtn = document.getElementById('closeAddCardModal');
    
    if (addBankCardBtn && addCardModal) {
        // Open modal when add bank card button is clicked
        addBankCardBtn.addEventListener('click', function() {
            // Hide payment methods modal
            if (paymentMethodsModal) {
                paymentMethodsModal.style.display = 'none';
            }
            
            // Show add card modal
            addCardModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling behind modal
        });
        
        // Close modal when X is clicked
        if (closeAddCardModalBtn) {
            closeAddCardModalBtn.addEventListener('click', function() {
                addCardModal.style.display = 'none';
                
                // Show payment methods modal again
                if (paymentMethodsModal) {
                    paymentMethodsModal.style.display = 'block';
                } else {
                    document.body.style.overflow = 'auto'; // Re-enable scrolling
                }
            });
        }
        
        // Close modal when clicking outside the modal content
        window.addEventListener('click', function(event) {
            if (event.target === addCardModal) {
                addCardModal.style.display = 'none';
                
                // Show payment methods modal again
                if (paymentMethodsModal) {
                    paymentMethodsModal.style.display = 'block';
                } else {
                    document.body.style.overflow = 'auto'; // Re-enable scrolling
                }
            }
        });
    }

    // Card number validation and card type detection
    const cardNumberInput = document.getElementById('card_number');
    const cardTypeInput = document.getElementById('card_type');
    
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            // Remove non-numeric characters
            this.value = this.value.replace(/\D/g, '');
            
            // Detect card type based on first digit
            const firstDigit = this.value.charAt(0);
            let cardType = 'visa'; // Default
            
            if (firstDigit === '4') {
                cardType = 'visa';
            } else if (firstDigit === '5') {
                cardType = 'mastercard';
            } else if (firstDigit === '3') {
                cardType = 'amex';
            } else if (firstDigit === '6') {
                cardType = 'discover';
            }
            
            cardTypeInput.value = cardType;
        });
    }

    // Expiry date validation
    const expiryMonthInput = document.getElementById('expiry_month');
    const expiryYearInput = document.getElementById('expiry_year');
    
    if (expiryMonthInput) {
        expiryMonthInput.addEventListener('input', function() {
            // Remove non-numeric characters
            this.value = this.value.replace(/\D/g, '');
            
            // Ensure month is between 1 and 12
            const month = parseInt(this.value);
            if (month > 12) {
                this.value = '12';
            } else if (month < 1 && this.value.length === 2) {
                this.value = '01';
            }
            
            // Auto-focus to year field when 2 digits are entered
            if (this.value.length === 2 && expiryYearInput) {
                expiryYearInput.focus();
            }
        });
    }
    
    if (expiryYearInput) {
        expiryYearInput.addEventListener('input', function() {
            // Remove non-numeric characters
            this.value = this.value.replace(/\D/g, '');
        });
    }

    // CVV validation
    const cvvInput = document.getElementById('cvv');
    
    if (cvvInput) {
        cvvInput.addEventListener('input', function() {
            // Remove non-numeric characters
            this.value = this.value.replace(/\D/g, '');
        });
    }

    // Add card form submission
    const addCardForm = document.getElementById('add-card-form');
    
    if (addCardForm) {
        addCardForm.addEventListener('submit', function(event) {
            // Validate form
            const cardNumber = document.getElementById('card_number').value;
            const holderName = document.getElementById('holder_name').value;
            const expiryMonth = document.getElementById('expiry_month').value;
            const expiryYear = document.getElementById('expiry_year').value;
            const cvv = document.getElementById('cvv').value;
            
            if (!cardNumber || cardNumber.length < 13) {
                alert('Please enter a valid card number');
                event.preventDefault();
                return false;
            }
            
            if (!holderName) {
                alert('Please enter the card holder name');
                event.preventDefault();
                return false;
            }
            
            if (!expiryMonth || !expiryYear) {
                alert('Please enter a valid expiry date');
                event.preventDefault();
                return false;
            }
            
            if (!cvv || cvv.length < 3) {
                alert('Please enter a valid CVV');
                event.preventDefault();
                return false;
            }
            
            // If validation passes, form will submit normally
            return true;
        });
    }

    // Delete card functionality
    document.querySelectorAll('.delete-card-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!confirm('Are you sure you want to delete this card?')) {
                event.preventDefault();
                return false;
            }
            return true;
        });
    });

    // PayPal form validation
    const paypalForm = document.getElementById('paypal-email-form');
    if (paypalForm) {
        paypalForm.addEventListener('submit', function(event) {
            const paypalEmail = document.getElementById('paypal_email').value;
            if (!paypalEmail || !paypalEmail.includes('@')) {
                alert('Please enter a valid PayPal email address');
                event.preventDefault();
                return false;
            }
            return true;
        });
    }

    // Gallery image input handling
    const galleryImageInput = document.getElementById('gallery_image_input');
    if (galleryImageInput) {
        galleryImageInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                // Handle multiple files
                Array.from(this.files).forEach(file => {
                    const formData = new FormData();
                    formData.append('gallery_image', file);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    
                    // Create preview immediately
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Create a new gallery item for preview
                        const galleryItem = document.createElement('div');
                        galleryItem.className = 'gallery-item uploading';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Gallery Image';
                        
                        const loadingDiv = document.createElement('div');
                        loadingDiv.className = 'loading-overlay';
                        loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        
                        galleryItem.appendChild(img);
                        galleryItem.appendChild(loadingDiv);
                        
                        // Add to gallery before the add-more button
                        const gallery = document.querySelector('.profile-gallery');
                        const addMoreBtn = document.querySelector('.gallery-item.add-more');
                        gallery.insertBefore(galleryItem, addMoreBtn);
                    };
                    reader.readAsDataURL(file);
                    
                    // Upload the image via AJAX
                    fetch('/profile/upload-gallery-image', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Find the uploading item and update it
                        const uploadingItem = document.querySelector('.gallery-item.uploading');
                        if (uploadingItem) {
                            uploadingItem.classList.remove('uploading');
                            uploadingItem.querySelector('.loading-overlay').remove();
                            
                            if (data.success) {
                                // Update the image src to the actual stored image
                                const img = uploadingItem.querySelector('img');
                                img.src = data.url + '?v=' + Date.now();
                                
                                // Add delete button
                                const deleteBtn = document.createElement('div');
                                deleteBtn.className = 'delete-gallery-image';
                                deleteBtn.setAttribute('data-path', data.path);
                                deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                                
                                // Add delete event listener
                                deleteBtn.addEventListener('click', function() {
                                    deleteGalleryImage(this);
                                });
                                
                                uploadingItem.appendChild(deleteBtn);
                                
                                // Hide add more button if we've reached the maximum
                                const galleryItems = document.querySelectorAll('.gallery-item:not(.add-more)');
                                if (galleryItems.length >= 6) {
                                    document.querySelector('.gallery-item.add-more').style.display = 'none';
                                }
                            } else {
                                // Remove the failed upload item
                                uploadingItem.remove();
                                alert('Failed to upload image: ' + (data.message || 'Unknown error'));
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Remove the failed upload item
                        const uploadingItem = document.querySelector('.gallery-item.uploading');
                        if (uploadingItem) {
                            uploadingItem.remove();
                        }
                        alert('Failed to upload image: ' + error.message);
                    });
                });
                
                // Clear the input
                this.value = '';
            }
        });
    }

    // Add more photos functionality (fallback)
    const addMoreBtn = document.querySelector('.add-more-btn');
    if (addMoreBtn) {
        addMoreBtn.addEventListener('click', function() {
            const galleryInput = document.getElementById('gallery_image_input');
            if (galleryInput) {
                galleryInput.click();
                return;
            }
            // Create a file input element
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none';
            
            // Append it to the body
            document.body.appendChild(fileInput);
            
            // Trigger click on the file input
            fileInput.click();
            
            // Handle file selection
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const formData = new FormData();
                    formData.append('gallery_image', this.files[0]);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    
                    // Show loading indicator
                    const loadingIndicator = document.createElement('div');
                    loadingIndicator.className = 'loading-indicator';
                    loadingIndicator.textContent = 'Uploading...';
                    addMoreBtn.appendChild(loadingIndicator);
                    
                    // Upload the image via AJAX
                    fetch('/profile/upload-gallery-image', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        // Check if the response is JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        } else {
                            // If it's not JSON, handle the error
                            throw new Error('Server returned a non-JSON response');
                        }
                    })
                    .then(data => {
                        // Remove loading indicator
                        if (loadingIndicator) {
                            loadingIndicator.remove();
                        }
                        
                        if (data.success) {
                            // Create a new gallery item
                            const galleryItem = document.createElement('div');
                            galleryItem.className = 'gallery-item';
                            
                            // Create image
                            const img = document.createElement('img');
                            img.src = data.url;
                            img.alt = 'Gallery Image';
                            
                            // Create delete button
                            const deleteBtn = document.createElement('div');
                            deleteBtn.className = 'delete-gallery-image';
                            deleteBtn.setAttribute('data-path', data.path);
                            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                            
                            // Add delete event listener
                            deleteBtn.addEventListener('click', function() {
                                deleteGalleryImage(this);
                            });
                            
                            // Add elements to gallery item
                            galleryItem.appendChild(img);
                            galleryItem.appendChild(deleteBtn);
                            
                            // Add gallery item to gallery
                            const gallery = document.querySelector('.profile-gallery');
                            gallery.insertBefore(galleryItem, addMoreBtn);
                            
                            // Hide add more button if we've reached the maximum number of gallery images
                            const galleryItems = gallery.querySelectorAll('.gallery-item:not(.add-more)');
                            if (galleryItems.length >= 6) {
                                addMoreBtn.style.display = 'none';
                            }
                        } else {
                            alert('Failed to upload image: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        // Remove loading indicator
                        if (loadingIndicator) {
                            loadingIndicator.remove();
                        }
                        
                        console.error('Error:', error);
                        alert('Failed to upload image: ' + error.message);
                    });
                }
                
                // Remove the file input from the DOM
                document.body.removeChild(fileInput);
            });
        });
    }
    
    // Delete gallery image functionality
    document.querySelectorAll('.delete-gallery-image').forEach(button => {
        button.addEventListener('click', function() {
            deleteGalleryImage(this);
        });
    });
    
    // Function to delete a gallery image
    function deleteGalleryImage(button) {
        // Check if this is a default image
        if (button.classList.contains('default-image')) {
            if (confirm('Are you sure you want to delete this image?')) {
                // For default images, just remove from DOM
                const galleryItem = button.closest('.gallery-item');
                galleryItem.remove();
                
                // Show add more button if it was hidden
                const addMoreBtn = document.querySelector('.gallery-item.add-more');
                if (addMoreBtn && addMoreBtn.style.display === 'none') {
                    addMoreBtn.style.display = '';
                }
            }
            return;
        }
        
        const imagePath = button.getAttribute('data-path');
        
        if (confirm('Are you sure you want to delete this image?')) {
            // Show loading indicator
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            const formData = new FormData();
            formData.append('image_path', imagePath);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Delete the image via AJAX
            fetch('/profile/delete-gallery-image', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                // Check if the response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If it's not JSON, just check if the status is OK
                    if (response.ok) {
                        return { success: true };
                    }
                    throw new Error('Server returned a non-JSON response');
                }
            })
            .then(data => {
                if (data.success) {
                    // Remove the gallery item from the DOM
                    const galleryItem = button.closest('.gallery-item');
                    galleryItem.remove();
                    
                    // Show add more button if it was hidden
                    const addMoreBtn = document.querySelector('.gallery-item.add-more');
                    if (addMoreBtn && addMoreBtn.style.display === 'none') {
                        addMoreBtn.style.display = '';
                    }
                } else {
                    alert('Failed to delete image');
                    
                    // Restore delete button
                    button.innerHTML = '<i class="fas fa-trash"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete image: ' + error.message);
                
                // Restore delete button
                button.innerHTML = '<i class="fas fa-trash"></i>';
            });
        }
    }

    // Initialize Bootstrap's modal (if available)
    let bootstrap;
    try {
        bootstrap = window.bootstrap;
    } catch (e) {
        bootstrap = undefined;
    }
});
/**
 * Profile Image Modal
 * 
 * This script handles the functionality for the profile image gallery modal.
 * It allows users to view profile images and gallery images in a slideshow format.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const modal = document.getElementById('profile-image-modal');
    const mainImage = document.getElementById('main-profile-image');
    const thumbnailContainer = document.querySelector('.thumbnail-container');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    // Array to store all images (profile + gallery)
    let allImages = [];
    let currentIndex = 0;
    
    // Function to open modal with a specific image
    window.openProfileImageModal = function(clickedImageSrc, galleryImages = []) {
        // Reset the images array and current index
        allImages = [];
        currentIndex = 0;
        
        // Add the main profile image first
        allImages.push(clickedImageSrc);
        
        // Add gallery images if they exist
        if (galleryImages && galleryImages.length > 0) {
            allImages = allImages.concat(galleryImages);
        }
        
        // Set current index based on which image was clicked
        currentIndex = allImages.findIndex(src => src === clickedImageSrc);
        if (currentIndex === -1) currentIndex = 0;
        
        // Update the main image
        mainImage.src = allImages[currentIndex];
        
        // Create thumbnails
        renderThumbnails();
        
        // Show modal with animation
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        
        // Update navigation button states
        updateNavButtons();
    };
    
    // Function to render thumbnails
    function renderThumbnails() {
        thumbnailContainer.innerHTML = '';
        
        allImages.forEach((src, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.classList.add('thumbnail');
            if (index === currentIndex) {
                thumbnail.classList.add('active');
            }
            
            const img = document.createElement('img');
            img.src = src;
            img.alt = 'Thumbnail';
            thumbnail.appendChild(img);
            
            thumbnail.addEventListener('click', () => {
                currentIndex = index;
                updateMainImage();
                updateThumbnailActive();
                updateNavButtons();
            });
            
            thumbnailContainer.appendChild(thumbnail);
        });
    }
    
    // Function to update active thumbnail
    function updateThumbnailActive() {
        const thumbnails = thumbnailContainer.querySelectorAll('.thumbnail');
        thumbnails.forEach((thumb, index) => {
            if (index === currentIndex) {
                thumb.classList.add('active');
                // Scroll the active thumbnail into view
                thumb.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            } else {
                thumb.classList.remove('active');
            }
        });
    }
    
    // Function to update main image
    function updateMainImage() {
        mainImage.src = allImages[currentIndex];
    }
    
    // Function to update navigation buttons (disable if at the end)
    function updateNavButtons() {
        // Hide navigation buttons if there's only one image
        if (allImages.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            return;
        }
        
        prevBtn.style.display = 'flex';
        nextBtn.style.display = 'flex';
        
        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex === allImages.length - 1;
        
        prevBtn.style.opacity = prevBtn.disabled ? '0.3' : '1';
        nextBtn.style.opacity = nextBtn.disabled ? '0.3' : '1';
    }
    
    // Previous button click handler
    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            updateMainImage();
            updateThumbnailActive();
            updateNavButtons();
        }
    });
    
    // Next button click handler
    nextBtn.addEventListener('click', function() {
        if (currentIndex < allImages.length - 1) {
            currentIndex++;
            updateMainImage();
            updateThumbnailActive();
            updateNavButtons();
        }
    });
    

    
    // Click outside to close
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(event) {
        if (modal.classList.contains('show')) {
            if (event.key === 'ArrowLeft' && currentIndex > 0) {
                currentIndex--;
                updateMainImage();
                updateThumbnailActive();
                updateNavButtons();
            } else if (event.key === 'ArrowRight' && currentIndex < allImages.length - 1) {
                currentIndex++;
                updateMainImage();
                updateThumbnailActive();
                updateNavButtons();
            } else if (event.key === 'Escape') {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }
    });
});
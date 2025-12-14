document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown functionality
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && e.target !== profileButton) {
                profileDropdown.classList.remove('active');
            }
        });
    }
    // Notification dropdown functionality
  const notificationButton = document.getElementById("notificationButton")
  const notificationDropdown = document.getElementById("notificationDropdown")

  if (notificationButton && notificationDropdown) {
    notificationButton.addEventListener("click", (e) => {
      e.stopPropagation()
      notificationDropdown.classList.toggle("active")

      // Close profile dropdown when opening notification dropdown
      if (profileDropdown) {
        profileDropdown.classList.remove("active")
      }
    })

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (!notificationDropdown.contains(e.target) && e.target !== notificationButton) {
        notificationDropdown.classList.remove("active")
      }
    })
  }

  // Menu dropdown functionality
  const menuButton = document.getElementById("menuButton")
  const menuDropdown = document.getElementById("menuDropdown")

  if (menuButton && menuDropdown) {
    menuButton.addEventListener("click", (e) => {
      e.stopPropagation()
      menuDropdown.classList.toggle("active")

      // Close other dropdowns
      if (profileDropdown) profileDropdown.classList.remove("active")
      if (notificationDropdown) notificationDropdown.classList.remove("active")
      const messagesDropdown = document.getElementById("messagesDropdown")
      if (messagesDropdown) messagesDropdown.classList.remove("active")
    })

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (!menuDropdown.contains(e.target) && e.target !== menuButton) {
        menuDropdown.classList.remove("active")
      }
    })
  }

  // Messages dropdown functionality
  const messagesButton = document.getElementById("messagesButton")
  const messagesDropdown = document.getElementById("messagesDropdown")

  if (messagesButton && messagesDropdown) {
    messagesButton.addEventListener("click", (e) => {
      e.stopPropagation()
      messagesDropdown.classList.toggle("active")

      // Close other dropdowns
      if (profileDropdown) profileDropdown.classList.remove("active")
      if (notificationDropdown) notificationDropdown.classList.remove("active")
      if (menuDropdown) menuDropdown.classList.remove("active")
    })

    // Close dropdown when clicking outside
    document.addEventListener("click", (e) => {
      if (!messagesDropdown.contains(e.target) && e.target !== messagesButton) {
        messagesDropdown.classList.remove("active")
      }
    })
  }
  
  // Search bar functionality
  const searchButton = document.getElementById("searchButton")
  const searchBar = document.getElementById("searchBar")
  const searchInput = document.getElementById("searchInput")
  const clearSearchButton = document.getElementById("clearSearchButton")
  const navLogo = document.getElementById("navLogo")
  const mobileMenuBtn = document.getElementById("mobileMenuToggle")

  if (searchButton && searchBar && searchInput && clearSearchButton) {
    // Expand search bar when clicking the search button
    searchButton.addEventListener("click", () => {
      if (navLogo) navLogo.classList.add("hidden")
      if (mobileMenuBtn) mobileMenuBtn.classList.add("hidden")
      searchBar.classList.add("expanded")
      searchInput.focus()
    })

    // Clear search input when clicking the clear button
    clearSearchButton.addEventListener("click", () => {
      searchInput.value = ""
      searchInput.focus()
    })

    // Function to close search bar and show hidden elements
    const closeSearchBar = () => {
      searchBar.classList.remove("expanded")
      if (navLogo) navLogo.classList.remove("hidden")
      if (mobileMenuBtn) mobileMenuBtn.classList.remove("hidden")
    }

    // Close search bar when clicking outside
    document.addEventListener("click", (e) => {
      const isClickInside = searchBar.contains(e.target)

      if (!isClickInside && searchBar.classList.contains("expanded") && searchInput.value === "") {
        closeSearchBar()
      }
    })

    // Handle escape key to close search
    searchInput.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        searchInput.value = ""
        closeSearchBar()
      }
    })
  }

  // Mobile menu functionality
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
  
  if (mobileMenuToggle && mobileMenuOverlay) {
    mobileMenuToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      mobileMenuToggle.classList.toggle('active');
      mobileMenuOverlay.classList.toggle('active');
      
      // Toggle hamburger icon
      const icon = mobileMenuToggle.querySelector('i');
      if (mobileMenuOverlay.classList.contains('active')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
      } else {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
        document.body.style.overflow = ''; // Restore scrolling
      }
      
      // Close other dropdowns
      if (profileDropdown) profileDropdown.classList.remove('active');
      if (notificationDropdown) notificationDropdown.classList.remove('active');
      if (menuDropdown) menuDropdown.classList.remove('active');
      const messagesDropdown = document.getElementById('messagesDropdown');
      if (messagesDropdown) messagesDropdown.classList.remove('active');
    });
    
    // Close mobile menu when clicking on menu items
    const mobileMenuItems = mobileMenuOverlay.querySelectorAll('.mobile-menu-item');
    mobileMenuItems.forEach(item => {
      item.addEventListener('click', function() {
        mobileMenuToggle.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        const icon = mobileMenuToggle.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
        document.body.style.overflow = ''; // Restore scrolling
      });
    });
    
    // Close mobile menu on window resize if screen becomes larger
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768 && mobileMenuOverlay.classList.contains('active')) {
        mobileMenuToggle.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        const icon = mobileMenuToggle.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
        document.body.style.overflow = ''; // Restore scrolling
      }
    });
  }

  // Settings modal functionality
  const settingsButton = document.getElementById('settingsButton');
  const mobileSettingsButton = document.getElementById('mobileSettingsButton');
  const settingsModal = document.getElementById('settingsModal');
  const closeSettingsModal = document.getElementById('closeSettingsModal');
  const saveChangesBtn = document.querySelector('.save-changes-btn');

  if (settingsModal && closeSettingsModal) {
    // Function to open settings modal
    function openSettingsModal(e) {
      e.preventDefault();
      e.stopPropagation();
      settingsModal.classList.add('active');
      document.body.style.overflow = 'hidden'; // Prevent background scrolling
      
      // Add class for mobile detection
      if (window.innerWidth <= 768) {
        document.body.classList.add('settings-modal-mobile-open');
      }
      
      // Close other dropdowns and mobile menu
      if (profileDropdown) profileDropdown.classList.remove('active');
      if (notificationDropdown) notificationDropdown.classList.remove('active');
      if (menuDropdown) menuDropdown.classList.remove('active');
      const messagesDropdown = document.getElementById('messagesDropdown');
      if (messagesDropdown) messagesDropdown.classList.remove('active');
      
      // Close mobile menu if open
      if (mobileMenuOverlay && mobileMenuOverlay.classList.contains('active')) {
        mobileMenuToggle.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        const icon = mobileMenuToggle.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-times');
          icon.classList.add('fa-bars');
        }
      }
    }
    
    // Open settings modal from desktop menu
    if (settingsButton) {
      settingsButton.addEventListener('click', openSettingsModal);
    }
    
    // Open settings modal from mobile menu
    if (mobileSettingsButton) {
      mobileSettingsButton.addEventListener('click', openSettingsModal);
    }

    // Close settings modal
    closeSettingsModal.addEventListener('click', function() {
      settingsModal.classList.remove('active');
      document.body.style.overflow = ''; // Restore scrolling
      document.body.classList.remove('settings-modal-mobile-open');
    });

    // Close modal when clicking outside
    settingsModal.addEventListener('click', function(e) {
      if (e.target === settingsModal) {
        settingsModal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
        document.body.classList.remove('settings-modal-mobile-open');
      }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && settingsModal.classList.contains('active')) {
        settingsModal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
        document.body.classList.remove('settings-modal-mobile-open');
      }
    });
  }

  // Settings functionality
  const notificationSoundToggle = document.getElementById('notificationSound');
  const pauseProfileToggle = document.getElementById('pauseProfile');
  const languageRadios = document.querySelectorAll('input[name="language"]');

  // Update toggle text based on state
  function updateToggleText() {
    const soundToggleText = document.querySelector('#notificationSound').closest('.settings-item').querySelector('.toggle-text');
    const pauseToggleText = document.querySelector('#pauseProfile').closest('.settings-item').querySelector('.toggle-text');
    
    if (soundToggleText) {
      soundToggleText.textContent = notificationSoundToggle.checked ? 'Sound On' : 'Sound Off';
    }
    
    if (pauseToggleText) {
      pauseToggleText.textContent = pauseProfileToggle.checked ? 'On Vacation Mode' : 'Off Vacation Mode';
    }
  }

  if (notificationSoundToggle) {
    notificationSoundToggle.addEventListener('change', updateToggleText);
  }

  if (pauseProfileToggle) {
    pauseProfileToggle.addEventListener('change', updateToggleText);
  }

  // Save changes functionality
  if (saveChangesBtn) {
    saveChangesBtn.addEventListener('click', function() {
      // Here you can add AJAX call to save settings to backend
      // For now, just show a simple feedback
      const originalText = saveChangesBtn.textContent;
      saveChangesBtn.textContent = 'Saved!';
      saveChangesBtn.style.backgroundColor = '#4CAF50';
      
      setTimeout(() => {
        saveChangesBtn.textContent = originalText;
        saveChangesBtn.style.backgroundColor = '#00BCD4';
        settingsModal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
        document.body.classList.remove('settings-modal-mobile-open');
      }, 1500);
    });
  }

});
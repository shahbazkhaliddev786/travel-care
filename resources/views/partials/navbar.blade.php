<nav class="navbar">
    <div class="nav-left">
        
        <!-- Mobile Menu Toggle -->
        <button id="mobileMenuToggle" class="mobile-menu-toggle">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a href="{{ url('/home') }}" class="logo" id="navLogo">
            <img src="{{ asset('assets/logo-with-txt.svg') }}" alt="TravelCare Logo">
        </a>

        <div class="menu-container">
            <button id="menuButton" class="menu-btn">
                MENU <i class="fa-solid fa-chevron-down"></i>
            </button>
            
            <div id="menuDropdown" class="menu-dropdown">
                @auth
                    @unless(Auth::user()->isDoctor())
                        <a href="#" class="menu-item">
                            <div class="menu-icon">
                                <i class="fa-solid fa-plane"></i>
                            </div>
                            <span>Safe Travel</span>
                        </a>
                    @endunless
                @else
                    <a href="#" class="menu-item">
                        <div class="menu-icon">
                            <i class="fa-solid fa-plane"></i>
                        </div>
                        <span>Safe Travel</span>
                    </a>
                @endauth
                <a href="#" class="menu-item" id="settingsButton">
                    <div class="menu-icon">
                        <i class="fa-solid fa-gear"></i>
                    </div>
                    <span>Settings</span>
                </a>
                <a href="#" class="menu-item">
                    <div class="menu-icon">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <span>Assistance</span>
                </a>
                <a href="#" class="menu-item">
                    <div class="menu-icon">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <span>Additional Services</span>
                </a>
            </div>
        </div>
        
        
        @auth
            @unless(Auth::user()->isDoctor() || Auth::user()->isLaboratory())
                <div class="nav-search-container">
                    <form method="GET" action="{{ route('category') }}" id="searchBar" class="nav-search-bar">
                        <button type="button" id="searchButton" class="nav-search-button">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <div class="nav-search-input-container">
                            <input type="text" id="searchInput" name="search" class="nav-search-input" placeholder="Search..." value="{{ request('search') }}" required>
                            <button type="button" id="clearSearchButton" class="clear-search-button">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </form>
                </div>
            @endunless
        @else
            <div class="nav-search-container">
                <form method="GET" action="{{ route('category') }}" id="searchBar" class="nav-search-bar">
                    <button type="button" id="searchButton" class="nav-search-button">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <div class="nav-search-input-container">
                        <input type="text" id="searchInput" name="search" class="nav-search-input" placeholder="Search..." value="{{ request('search') }}" required>
                        <button type="button" id="clearSearchButton" class="clear-search-button">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </form>
            </div>
        @endauth
    </div>


    <div class="nav-right">
        <div class="nav-messages-container">
            <button id="messagesButton" class="nav-icon">
                <i class="fas fa-envelope"></i>
                <span class="notification-badge">0</span>
            </button>
            
            <div id="messagesDropdown" class="nav-messages-dropdown">
                <div class="nav-messages-header">
                    <h4>Messages (0)</h4>
                    <a href="#" class="go-to-chats">Go To Chats</a>
                </div>
                
                <div class="nav-messages-list">
                    {{-- <div class="nav-message-item">
                        <div class="nav-message-avatar">
                            <img src="{{ asset('/assets/images/top-doctor1.png') }}" alt="Dr. Martina Menganite">
                            <span class="nav-message-badge">3</span>
                        </div>
                        <div class="nav-message-content">
                            <h5 class="nav-message-sender">Dr. Martina Menganite</h5>
                            <p class="nav-message-preview">See you later. Take care!</p>
                        </div>
                    </div>
                    
                    <div class="nav-message-item">
                        <div class="nav-message-avatar">
                            <img src="{{ asset('/assets/images/top-doctor.png') }}" alt="Dr. Fernando A. Perez">
                            <span class="nav-message-badge">1</span>
                        </div>
                        <div class="nav-message-content">
                            <h5 class="nav-message-sender">Dr. Fernando A. Perez</h5>
                            <p class="nav-message-preview">Hello! I'll be waiting for you at 9:00 am at Cowdray American-British Medical Center.</p>
                        </div>
                    </div>
                    
                    <div class="nav-message-item">
                        <div class="nav-message-avatar">
                            <img src="{{ asset('/assets/images/top-doctor3.png') }}" alt="Dr. Skylar Carder">
                        </div>
                        <div class="nav-message-content">
                            <h5 class="nav-message-sender">Dr. Skylar Carder</h5>
                            <p class="nav-message-preview">You: Thank you for all the care that you have given me over the past 5 years. You have mad...</p>
                        </div>
                    </div>
                    
                    <div class="nav-message-item">
                        <div class="nav-message-avatar">
                            <img src="{{ asset('/assets/images/top-doctor2.png') }}" alt="Dr. Maren Korsgaard">
                        </div>
                        <div class="nav-message-content">
                            <h5 class="nav-message-sender">Dr. Maren Korsgaard</h5>
                            <p class="nav-message-preview">You: Thank you for all the care that you have given me over the past 5 years. You have mad...</p>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>





        <div class="notification-container">
            <button id="notificationButton" class="nav-icon">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">0</span>
            </button>
            
            <div id="notificationDropdown" class="notification-dropdown">
                {{-- <div class="notification-section">
                    <h4 class="notification-date">Today</h4>
                    
                    <div class="notification-item">
                        <div class="notification-icon analysis-icon">
                            <i class="fa-solid fa-flask"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">Your Analysis Is Ready</h5>
                            <p class="notification-text">Your Estradiol (E2) analysis is now available to view.</p>
                        </div>
                    </div>
                    
                    <div class="notification-item">
                        <div class="notification-icon alarm-icon">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">Appointment Alarm</h5>
                            <p class="notification-text">Your appointment with Dr. Martha Zoldana will start in 15 minutes. Stay with appp and take care.</p>
                        </div>
                    </div>
                    
                    <div class="notification-item">
                        <div class="notification-icon confirmed-icon">
                            <i class="fa-regular fa-bell"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">Appointment Confirmed</h5>
                            <p class="notification-text">Your appointment with Dr. Martha Zoldana is confirmed. She will call you soon.</p>
                        </div>
                    </div>
                </div>
                
                <div class="notification-section">
                    <h4 class="notification-date">7 July, 2023</h4>
                    
                    <div class="notification-item">
                        <div class="notification-icon alarm-icon">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">Appointment Alarm</h5>
                            <p class="notification-text">Your appointment with Dr. Martha Zoldana will start in 15 minutes. Stay with appp and take care.</p>
                        </div>
                    </div>
                    
                    <div class="notification-item">
                        <div class="notification-icon confirmed-icon">
                            <i class="fa-regular fa-bell"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">Appointment Confirmed</h5>
                            <p class="notification-text">Your appointment with Dr. Martha Zoldana is confirmed. She will call you soon.</p>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
        
            
        <div class="user-profile-container">
            <button id="profileButton" class="profile-button">
                @auth
                    @php
                        $profileImage = null;
                        $user = Auth::user();
                        
                        // Get profile image - doctors have priority over general profile_photo
                        if ($user->isDoctor() && $user->doctor && $user->doctor->profile_image) {
                            $profileImage = $user->doctor->profile_image;
                        } else {
                            $profileImage = $user->profile_photo;
                        }
                    @endphp
                    <img src="{{ $profileImage ? asset('storage/' . $profileImage) . '?v=' . time() : asset('assets/icons/profile.svg') }}" 
                         alt="Profile" 
                         class="profile-pic">
                @else
                    <img src="{{ asset('assets/icons/profile.svg') }}" alt="Profile" class="profile-pic">
                @endauth
            </button>
            
            <div id="profileDropdown" class="profile-dropdown">
                <div class="profile-header">
                    @auth
                        <h3>{{ Auth::user()->name }}</h3>
                        @if(Auth::user()->customerProfile && Auth::user()->customerProfile->phone_number)
                            <p>{{ Auth::user()->customerProfile->country_code }} {{ Auth::user()->customerProfile->phone_number }}</p>
                        @elseif(Auth::user()->doctor && Auth::user()->doctor->phone)
                            <p>{{ Auth::user()->doctor->country_code }} {{ Auth::user()->doctor->phone }}</p>
                        @else
                            <p>No phone number available</p>
                        @endif
                    @else
                        <h3>Guest User</h3>
                        <p>Please sign in</p>
                    @endauth
                </div>
                <div class="profile-menu">
                    @auth
                        <a href="{{ Auth::user()->isCustomer() ? route('profile') : (Auth::user()->isDoctor() ? route('profile.index') : (Auth::user()->isLaboratory() ? route('profile.index') : route('profile'))) }}" class="profile-menu-item">
                            <i class="fa-regular fa-user"></i>
                            <span>My Profile</span>
                        </a>
                    @else
                        <a href="{{ route('signin') }}" class="profile-menu-item">
                            <i class="fa-regular fa-user"></i>
                            <span>Sign In</span>
                        </a>
                    @endauth
                    <a href="/schedule" class="profile-menu-item">
                        <i class="fa-regular fa-calendar"></i>
                        <span>My Schedule</span>
                    </a>
                    <a href="/analysis" class="profile-menu-item">
                        <i class="fa-solid fa-flask"></i>
                        @auth
                            @if(Auth::user()->isDoctor())
                                <span>Analysis</span>
                            @else
                                <span>My Analysis</span>
                            @endif
                        @else
                            <span>My Analysis</span>
                        @endauth
                    </a>
                    <a href="/bills" class="profile-menu-item">
                        <i class="fa-regular fa-file-lines"></i>
                        @auth
                            @if(Auth::user()->isDoctor())
                                <span>My Payments</span>
                            @else
                                <span>My Bills</span>
                            @endif
                        @else
                            <span>My Bills</span>
                        @endauth
                    </a>
                    @auth
                        <a href="/logout" class="profile-menu-item">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Log Out</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    
    <!-- Settings Modal -->
    <div id="settingsModal" class="settings-modal">
        <div class="settings-modal-content">
            <button id="closeSettingsModal" class="close-settings-btn">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="settings-header">
                <h2>Settings</h2>
            </div>
            
            <div class="settings-body">
                <div class="settings-section">
                    <div class="settings-item">
                        <div>
                        <span class="settings-label">Need To Update Your Public Profile?</span>
                        <a href="{{ Auth::check() ? (Auth::user()->isCustomer() ? route('profile') : (Auth::user()->isDoctor() ? route('profile.index') : route('profile'))) : route('signin') }}" class="go-to-profile-btn">Go to My Profile</a>
                        </div>
                    </div>
                </div>
                
                <div class="settings-section">
                    <div class="settings-row">
                        <div class="settings-item">
                            <span class="settings-label">Notifications Sound</span>
                            <div class="toggle-container">
                                <div class="toggle-switch">
                                    <input type="checkbox" id="notificationSound" checked>
                                    <label for="notificationSound" class="toggle-label">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <span class="toggle-text">Sound On</span>
                            </div>
                        </div>
                        
                        <div class="settings-item">
                            <span class="settings-label">Language</span>
                            <div class="language-options">
                                <label class="radio-option">
                                    <input type="radio" name="language" value="english" checked>
                                    <span class="radio-custom"></span>
                                    <span class="radio-text">English</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="language" value="spanish">
                                    <span class="radio-custom"></span>
                                    <span class="radio-text">Spanish</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="settings-section">
                    <div class="settings-item">
                        <span class="settings-label">Pause The Profile</span>
                        <div class="toggle-container">
                            <div class="toggle-switch">
                                <input type="checkbox" id="pauseProfile">
                                <label for="pauseProfile" class="toggle-label">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <span class="toggle-text">On Vacation Mode</span>
                        </div>
                    </div>
                    <div class="settings-description">
                        <span>Recommend a doctor in your profile while you're unavailable (optional)</span>
                        <input type="text" class="doctor-recommendation" placeholder="@Doctor">
                    </div>
                </div>
            </div>
            
            <div class="settings-footer">
                <button class="save-changes-btn">Save Changes</button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="mobile-menu-overlay">
        
        <!-- Notifications Section -->
        <div class="mobile-menu-section">
            <a href="#" class="mobile-menu-item">
                <i class="fa-solid fa-bell"></i>
                <span>Notifications</span>
                <span class="notification-badge">0</span>
            </a>
        </div>
        
        <!-- Messages Section -->
        <div class="mobile-menu-section divider">
            <a href="#" class="mobile-menu-item">
                <i class="fa-solid fa-envelope"></i>
                <span>Messages</span>
                <span class="notification-badge">0</span>
            </a>
        </div>
        
        <!-- Profile Section -->
        <div class="mobile-menu-section divider">
            @auth
                <a href="{{ Auth::user()->isCustomer() ? route('profile') : (Auth::user()->isDoctor() ? route('profile.index') : (Auth::user()->isLaboratory() ? route('profile.index') : route('profile'))) }}" class="mobile-menu-item">
                    <i class="fa-regular fa-user"></i>
                    <span>My Profile</span>
                </a>
            @else
                <a href="{{ route('signin') }}" class="mobile-menu-item">
                    <i class="fa-regular fa-user"></i>
                    <span>Sign In</span>
                </a>
            @endauth
            <a href="/schedule" class="mobile-menu-item">
                <i class="fa-regular fa-calendar"></i>
                <span>My Schedule</span>
            </a>
            <a href="/analysis" class="mobile-menu-item">
                <i class="fa-solid fa-flask"></i>
                @auth
                    @if(Auth::user()->isDoctor())
                        <span>Analysis</span>
                    @else
                        <span>My Analysis</span>
                    @endif
                @else
                    <span>My Analysis</span>
                @endauth
            </a>
            <a href="/bills" class="mobile-menu-item">
                <i class="fa-regular fa-file-lines"></i>
                @auth
                    @if(Auth::user()->isDoctor())
                        <span>My Payments</span>
                    @else
                        <span>My Bills</span>
                    @endif
                @else
                    <span>My Bills</span>
                @endauth
            </a>
        </div>
        
        <!-- Additional Services Section -->
        <div class="mobile-menu-section divider">
            @auth
                @unless(Auth::user()->isDoctor())
                    <a href="#" class="mobile-menu-item">
                        <i class="fa-solid fa-plane"></i>
                        <span>Safe Travel</span>
                    </a>
                @endunless
            @else
                <a href="#" class="mobile-menu-item">
                    <i class="fa-solid fa-plane"></i>
                    <span>Safe Travel</span>
                </a>
            @endauth
            <a href="#" class="mobile-menu-item" id="mobileSettingsButton">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
            <a href="#" class="mobile-menu-item">
                <i class="fa-solid fa-headset"></i>
                <span>Assistance</span>
            </a>
            <a href="#" class="mobile-menu-item">
                <i class="fa-solid fa-list-check"></i>
                <span>Additional Services</span>
            </a>
        </div>
        
        @auth
            <div class="mobile-menu-section">
                <a href="/logout" class="mobile-menu-item">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Log Out</span>
                </a>
            </div>
        @endauth
    </div>
</nav>

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Firebase
    const firebaseConfig = {
        apiKey: "AIzaSyBLHZUMzNfGxKLgwHWJYsKWGvjuS2lbxl8",
        authDomain: "travel-care-5f873.firebaseapp.com",
        databaseURL: "https://travel-care-5f873-default-rtdb.firebaseio.com",
        projectId: "travel-care-5f873",
        storageBucket: "travel-care-5f873.firebasestorage.app",
        messagingSenderId: "649406915398",
        appId: "1:649406915398:web:46e7b8d9d2c5d0a6c23b3a"
    };
    
    console.log('Initializing Firebase with config:', firebaseConfig);
    
    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();
    
    console.log('Firebase initialized successfully');
    
    // Test Firebase connection
    database.ref('.info/connected').on('value', function(snap) {
        if (snap.val() === true) {
            console.log('✓ Firebase connected successfully');
            // Set user online when Firebase connects
            setUserOnline();
        } else {
            console.log('✗ Firebase not connected');
        }
    });
    
    // Set user online immediately on page load
    setUserOnline();
    
    // Handle window resize for responsive behavior
    window.addEventListener('resize', () => {
        // Close modal if switching from mobile to desktop
        if (window.innerWidth > 768 && chatModal && chatModal.classList.contains('show')) {
            closeChatModalHandler();
        }
    });
    
    // Get DOM elements
    const chatInput = document.querySelector('.chat-input input');
    const sendButton = document.querySelector('.send-btn');
    const attachButton = document.querySelector('.attach-btn');
    const messagesContainer = document.querySelector('.messages-container');
    const chatItems = document.querySelectorAll('.chat-item');
    
    // Modal elements
    const chatModal = document.getElementById('chatModal');
    const closeChatModal = document.getElementById('closeChatModal');
    const modalMessagesContainer = document.getElementById('modalMessagesContainer');
    const modalChatInput = document.getElementById('modalChatInput');
    const modalSendBtn = document.getElementById('modalSendBtn');
    const modalAttachBtn = document.getElementById('modalAttachBtn');
    const modalUserAvatar = document.getElementById('modalUserAvatar');
    const modalUserName = document.getElementById('modalUserName');
    const modalUserStatus = document.getElementById('modalUserStatus');
    const modalChatActions = document.getElementById('modalChatActions');

    // Chat data from Laravel
    const currentUser = window.chatData.currentUser;
    const currentChatId = window.chatData.currentChatId;
    let selectedChatWith = null;
    let currentFirebaseListener = null;
    let typingTimeout = null;
    let presenceListeners = new Map();
    
    // Cache for loaded messages to avoid duplicates
    const messageCache = new Map();
    
    // Preload messages for faster chat switching
    const chatMessageCache = new Map();
    
    // Mobile modal functions
    function openChatModal(chatItem) {
        if (window.innerWidth <= 768) {
            // Update modal header with selected chat info
            const userName = chatItem.querySelector('h3').textContent;
            const userImage = chatItem.querySelector('img').src;
            const statusBadge = chatItem.querySelector('.status-badge');
            const userStatus = statusBadge ? statusBadge.classList.contains('online') ? 'online' : 'offline' : 'offline';
            
            modalUserAvatar.src = userImage;
            modalUserName.textContent = userName;
            modalUserStatus.textContent = userStatus;
            modalUserStatus.className = `status ${userStatus}`;
            
            // Show call actions if user is doctor
            if (window.chatData.currentUser.role === 'doctor') {
                modalChatActions.style.display = 'flex';
            }
            
            // Sync messages from main container to modal
            syncMessagesToModal();
            
            // Show modal
            chatModal.classList.add('show');
            
            // Focus on modal input
            setTimeout(() => {
                modalChatInput.focus();
            }, 300);
        }
    }
    
    function closeChatModalHandler() {
        chatModal.classList.remove('show');
        // Sync messages back to main container
        syncMessagesFromModal();
    }
    
    function syncMessagesToModal() {
        modalMessagesContainer.innerHTML = messagesContainer.innerHTML;
        setTimeout(() => {
            modalMessagesContainer.scrollTop = modalMessagesContainer.scrollHeight;
        }, 100);
    }
    
    function syncMessagesFromModal() {
        messagesContainer.innerHTML = modalMessagesContainer.innerHTML;
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    }
    
    // Modal event listeners
    if (closeChatModal) {
        closeChatModal.addEventListener('click', closeChatModalHandler);
    }
    
    // Modal input handlers
    if (modalSendBtn) {
        modalSendBtn.addEventListener('click', () => {
            const message = modalChatInput.value.trim();
            if (message && selectedChatWith) {
                // Use the same send message function but with modal input
                const originalInput = chatInput.value;
                chatInput.value = message;
                modalChatInput.value = '';
                sendMessage();
                chatInput.value = originalInput;
            }
        });
    }
    
    if (modalChatInput) {
        modalChatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                modalSendBtn.click();
            }
        });
    }
    
    if (modalAttachBtn) {
        modalAttachBtn.addEventListener('click', () => {
            // Trigger the same file attachment logic
            attachButton.click();
        });
    }

    // Function to format time consistently (always 12-hour format)
    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        }).toLowerCase();
    }

    // Function to add new message to UI with optimized rendering
    function addMessage(messageData, isSent = true) {
        const messageTime = typeof messageData.timestamp === 'string' ? 
            new Date(messageData.timestamp) : 
            new Date(messageData.timestamp * 1000);
        
        const senderName = messageData.sender_name || 'Unknown';
        
        // Get correct profile photo
        let senderPhoto;
        if (isSent) {
            senderPhoto = currentUser.profile_photo || '/assets/icons/default-avatar.svg';
        } else {
            // For received messages, get the photo from selected chat participant
            const participant = window.chatData.chatParticipants.find(p => p.id === messageData.sender_id);
            senderPhoto = participant?.profile_photo || '/assets/icons/default-avatar.svg';
        }
        
        // Ensure profile photo URL is absolute
        if (senderPhoto && !senderPhoto.startsWith('http') && !senderPhoto.startsWith('/')) {
            senderPhoto = '/' + senderPhoto;
        }
        
        const messageHTML = `
            <div class="message ${isSent ? 'sent' : 'received'}" data-message-id="${messageData.id || Date.now()}">
                ${!isSent ? `
                    <div class="message-avatar">
                        <img src="${senderPhoto}" alt="${senderName}" onerror="this.src='/assets/icons/default-avatar.svg'">
                    </div>
                ` : ''}
                <div class="message-content">
                    <p>${escapeHtml(messageData.message || messageData)}</p>
                    <span class="message-time">${formatTime(messageTime)}</span>
                </div>
            </div>
        `;

        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        scrollToBottom();
        
        // Also add to modal if it's open
        if (modalMessagesContainer && window.innerWidth <= 768) {
            modalMessagesContainer.insertAdjacentHTML('beforeend', messageHTML);
            modalMessagesContainer.scrollTop = modalMessagesContainer.scrollHeight;
        }
    }
    
    // Function to escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Function to scroll to bottom of messages
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Optimized send message function with instant UI feedback
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message || !selectedChatWith) {
            if (!message) {
                chatInput.focus();
            }
            console.log('Cannot send message - missing data:', { message: !!message, selectedChatWith });
            return;
        }
        
        // Clear input immediately for better UX
        const originalMessage = message;
            chatInput.value = '';
        
        // Add message to UI immediately for instant feedback
        const tempMessageData = {
            id: 'temp_' + Date.now(),
            sender_id: currentUser.id,
            sender_name: currentUser.name,
            message: originalMessage,
            timestamp: new Date().toISOString(),
            type: 'text'
        };
        
        addMessage(tempMessageData, true);
        
        // Create message data for server
        const messageData = {
            chat_with: selectedChatWith,
            message: originalMessage,
            type: 'text'
        };
        
        console.log('Sending message:', messageData);
        
        // Send to server (without blocking UI)
        fetch('/chats/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.chatData.csrfToken
            },
            body: JSON.stringify(messageData)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Message send response:', data);
            
            if (data.success) {
                console.log('Message sent successfully');
                // Remove temporary message since real one will come from Firebase
                const tempElement = document.querySelector(`[data-message-id="${tempMessageData.id}"]`);
                if (tempElement) {
                    tempElement.remove();
                }
            } else {
                console.error('Failed to send message:', data.message);
                // Show error but keep the message in UI
                const tempElement = document.querySelector(`[data-message-id="${tempMessageData.id}"]`);
                if (tempElement) {
                    tempElement.style.opacity = '0.5';
                    tempElement.title = 'Failed to send - click to retry';
                }
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            // Show error indication
            const tempElement = document.querySelector(`[data-message-id="${tempMessageData.id}"]`);
            if (tempElement) {
                tempElement.style.opacity = '0.5';
                tempElement.title = 'Failed to send - click to retry';
            }
        });
        
        // Focus back on input
        chatInput.focus();
    }
    
    // Send message on button click
    sendButton.addEventListener('click', sendMessage);

    // Send message on Enter key
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Enhanced typing indicator with proper debouncing
    let isTyping = false;
    chatInput.addEventListener('input', () => {
        if (!isTyping && chatInput.value.trim()) {
            isTyping = true;
            setTypingStatus(true);
        }
        
        // Clear existing timeout
        clearTimeout(typingTimeout);
        
        // Set new timeout
        typingTimeout = setTimeout(() => {
            if (isTyping) {
                isTyping = false;
                setTypingStatus(false);
            }
        }, 1000);
    });

    // File attachment handling
    attachButton.addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.pdf,.doc,.docx,.jpg,.png';
        
        input.onchange = (e) => {
            const file = e.target.files[0];
            if (file) {
                const fileMessage = `
                    <div class="file-attachment">
                        <i class="fas fa-file"></i>
                        <span class="file-name">${file.name}</span>
                        <i class="fas fa-download"></i>
                    </div>
                `;
                addMessage(fileMessage);
            }
        };
        
        input.click();
    });

    // Add click handlers for profile images to navigate to public profile
    function addProfileImageClickHandlers() {
        // Handle chat list avatar clicks
        document.querySelectorAll('.chat-avatar img').forEach(img => {
            img.addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent chat item click
                const chatItem = img.closest('.chat-item');
                const userId = chatItem.dataset.chatId;
                window.location.href = `/user-profile/${userId}`;
            });
        });
        
        // Handle message avatar clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('.message-avatar img')) {
                e.stopPropagation();
                if (selectedChatWith) {
                    window.location.href = `/user-profile/${selectedChatWith}`;
                }
            }
        });
        
        // Handle chat header avatar clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('.chat-user img')) {
                e.stopPropagation();
                if (selectedChatWith) {
                    window.location.href = `/user-profile/${selectedChatWith}`;
                }
            }
        });
        
        // Handle modal avatar clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('#modalUserAvatar')) {
                e.stopPropagation();
                if (selectedChatWith) {
                    window.location.href = `/user-profile/${selectedChatWith}`;
                }
            }
        });
    }
    
    // Initialize profile image click handlers
    addProfileImageClickHandlers();

    // Optimized chat item selection with preloading
    chatItems.forEach(item => {
        item.addEventListener('click', () => {
            console.log('Chat item clicked:', item);
            
            // Remove active class from all items
            chatItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');

            // Update selected chat
            const newSelectedChatWith = parseInt(item.dataset.chatId);
            
            // If same chat is selected, don't reload
            if (selectedChatWith === newSelectedChatWith) {
                // Still open modal on mobile if same chat is clicked
                if (window.innerWidth <= 768) {
                    openChatModal(item);
                }
                return;
            }
            
            selectedChatWith = newSelectedChatWith;
            
            console.log('Selected chat with user ID:', selectedChatWith);
            console.log('Current user ID:', currentUser.id);
            
            // Update chat header immediately for instant feedback
            const userName = item.querySelector('h3').textContent;
            const userImage = item.querySelector('img').src;

            // Update UI immediately
            const chatHeaderImg = document.querySelector('.chat-user img');
            if (chatHeaderImg) {
                chatHeaderImg.src = userImage;
                chatHeaderImg.setAttribute('onerror', "this.onerror=null;this.src='/assets/icons/default-avatar.svg';");
            }
            const userInfoH2 = document.querySelector('.user-info h2');
            if (userInfoH2) {
                userInfoH2.textContent = userName;
            }
            
            // Load messages for this chat with optimized loading
            loadChatMessagesOptimized(selectedChatWith);
            
            // Open modal on mobile
            openChatModal(item);
        });
    });

    // Optimized message loading with caching
    function loadChatMessagesOptimized(chatWith) {
        console.log('Loading messages for chat with user ID:', chatWith);
        
        // Check cache first
        if (chatMessageCache.has(chatWith)) {
            console.log('Loading messages from cache');
            const cachedData = chatMessageCache.get(chatWith);
            displayMessages(cachedData);
            setupFirebaseListener(cachedData.chat_id);
            return;
        }
        
        // Show loading indicator
        messagesContainer.innerHTML = '<div class="loading-messages">Loading messages...</div>';
        
        fetch(`/chats/messages?chat_with=${chatWith}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received chat data:', data);
            
            if (data.messages) {
                // Cache the data
                chatMessageCache.set(chatWith, data);
                
                // Display messages
                displayMessages(data);
                
                // Setup Firebase listener for this chat
                console.log('Setting up Firebase listener for chat:', data.chat_id);
                setupFirebaseListener(data.chat_id);
            } else {
                // No messages but successful response
                displayMessages({ messages: [] });
                if (data.chat_id) {
                    setupFirebaseListener(data.chat_id);
                }
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            // Only show error if it's not a network timeout or if we have no cached data
            if (!chatMessageCache.has(chatWith)) {
                messagesContainer.innerHTML = '<div class="error-messages">Error loading messages. Please try again.</div>';
            }
        });
    }
    
    // Display messages with optimized rendering
    function displayMessages(data) {
        // Clear existing messages
        messagesContainer.innerHTML = '';
        
        // Add messages
        let currentDate = null;
        data.messages.forEach(message => {
            const messageDate = new Date(message.timestamp).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            if (currentDate !== messageDate) {
                messagesContainer.insertAdjacentHTML('beforeend', 
                    `<div class="message-date">${messageDate}</div>`);
                currentDate = messageDate;
            }
            
            const isSent = message.sender_id === currentUser.id;
            addMessage(message, isSent);
        });
        
        scrollToBottom();
        
        // Sync to modal if on mobile
        if (window.innerWidth <= 768 && modalMessagesContainer) {
            syncMessagesToModal();
        }
    }

    // Setup Firebase real-time listener with improved performance
    function setupFirebaseListener(chatId) {
        if (!chatId) {
            console.log('No chat ID provided for Firebase listener');
            return;
        }
        
        console.log('Setting up Firebase listener for chat:', chatId);
        
        // Remove existing listener if any
        if (currentFirebaseListener) {
            currentFirebaseListener.off();
            console.log('Removed previous Firebase listener');
        }
        
        const messagesRef = database.ref(`chats/${chatId}/messages`);
        currentFirebaseListener = messagesRef;
        
        // Listen for new messages
        messagesRef.on('child_added', (snapshot) => {
            const messageData = snapshot.val();
            messageData.id = snapshot.key;
            
            console.log('Received Firebase message:', messageData);
            
            // Only add if not already displayed
            const existingMessage = document.querySelector(`[data-message-id="${messageData.id}"]`);
            if (!existingMessage) {
                console.log('Adding new message to UI');
                
                // Add date separator if needed
                const messageDate = new Date(messageData.timestamp).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                const lastDateElement = messagesContainer.querySelector('.message-date:last-of-type');
                const lastDate = lastDateElement ? lastDateElement.textContent : '';
                
                if (lastDate !== messageDate) {
                    messagesContainer.insertAdjacentHTML('beforeend', 
                        `<div class="message-date">${messageDate}</div>`);
                }
                
                const isSent = messageData.sender_id === currentUser.id;
                addMessage(messageData, isSent);
            } else {
                console.log('Message already exists in UI, skipping');
            }
        });
        
        // Handle listener errors
        messagesRef.on('error', (error) => {
            console.error('Firebase listener error:', error);
        });
    }

    // Presence Management Functions
    function setUserOnline() {
        fetch('/chats/set-online', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.chatData.csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('User status set to online');
            }
        })
        .catch(error => {
            console.error('Error setting user online:', error);
        });
    }

    function setUserOffline() {
        fetch('/chats/set-offline', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.chatData.csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('User status set to offline');
            }
        })
        .catch(error => {
            console.error('Error setting user offline:', error);
        });
    }

    function setTypingStatus(typing) {
        fetch('/chats/set-typing', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.chatData.csrfToken
            },
            body: JSON.stringify({ typing: typing })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Typing status set to:', typing);
            }
        })
        .catch(error => {
            console.error('Error setting typing status:', error);
        });
    }

    // Setup presence listeners for all chat participants
    function setupPresenceListeners() {
        console.log('Setting up presence listeners for participants:', window.chatData.chatParticipants);
        
        window.chatData.chatParticipants.forEach(participant => {
            const userId = participant.id;
            console.log('Setting up presence listener for user:', userId);
            
            const presenceRef = database.ref(`presence/${userId}`);
            
            presenceRef.on('value', (snapshot) => {
                const presence = snapshot.val();
                console.log('Presence update for user', userId, ':', presence);
                if (presence) {
                    updateUserPresence(userId, presence);
                } else {
                    // If no presence data, assume offline
                    updateUserPresence(userId, { online: false, typing: false });
                }
            });
            
            presenceListeners.set(userId, presenceRef);
        });
    }

    // Update user presence in the UI
    function updateUserPresence(userId, presence) {
        console.log('Updating presence for user:', userId, presence);
        
        // Update in chat list
        const chatItem = document.querySelector(`[data-chat-id="${userId}"]`);
        if (chatItem) {
            const statusBadge = chatItem.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = `status-badge ${presence.online ? 'online' : 'offline'}`;
            }
        }
        
        // Update in chat header if this is the selected chat
        if (selectedChatWith === userId) {
            const statusElement = document.querySelector('.user-info .status');
            if (statusElement) {
                if (presence.typing) {
                    statusElement.textContent = 'Typing...';
                    statusElement.className = 'status typing';
                } else {
                    statusElement.textContent = presence.online ? 'Online' : 'Offline';
                    statusElement.className = `status ${presence.online ? 'online' : 'offline'}`;
                }
            }
        }
    }

    // Initialize chat
    if (window.chatData.chatParticipants.length > 0) {
        // Set the first participant as selected
        selectedChatWith = window.chatData.chatParticipants[0].id;
        
        // Generate correct chat ID for current user and first participant
        const correctChatId = generateChatId(currentUser.id, selectedChatWith);
        setupFirebaseListener(correctChatId);
        
        // Mark first chat as active
        const firstChatItem = document.querySelector('.chat-item');
        if (firstChatItem) {
            firstChatItem.classList.add('active');
        }
        
        // Setup presence listeners
        setupPresenceListeners();
        
        // Load initial messages for first chat
        loadChatMessagesOptimized(selectedChatWith);
    }
    
    // Helper function to generate consistent chat ID (same logic as backend)
    function generateChatId(userId1, userId2) {
        const ids = [parseInt(userId1), parseInt(userId2)];
        ids.sort((a, b) => a - b); // Sort numerically
        return 'chat_' + ids.join('_');
    }
    
    // Focus on input when page loads
    if (chatInput) {
        chatInput.focus();
    }

    // Initialize
    scrollToBottom();

    // Handle page visibility changes for presence
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            setUserOffline();
        } else {
            setUserOnline();
        }
    });

    // Handle page unload
    window.addEventListener('beforeunload', () => {
        setUserOffline();
        
        // Clear typing status
        if (isTyping) {
            setTypingStatus(false);
        }
        
        // Clean up Firebase listeners
        if (currentFirebaseListener) {
            currentFirebaseListener.off();
        }
        
        presenceListeners.forEach(listener => {
            listener.off();
        });
    });

    // Handle file drag and drop
    const dropZone = document.querySelector('.messages-container');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        [...files].forEach(file => {
            const fileMessage = `
                <div class="file-attachment">
                    <i class="fas fa-file"></i>
                    <span class="file-name">${file.name}</span>
                    <i class="fas fa-download"></i>
                </div>
            `;
            addMessage(fileMessage);
        });
    }

    // Preload messages for faster switching
    function preloadMessages() {
        window.chatData.chatParticipants.forEach(participant => {
            if (participant.id !== selectedChatWith) {
                // Preload in background
                setTimeout(() => {
                    fetch(`/chats/messages?chat_with=${participant.id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.messages) {
                            chatMessageCache.set(participant.id, data);
                            console.log(`Preloaded messages for user ${participant.id}`);
                        }
                    })
                    .catch(error => {
                        console.log(`Failed to preload messages for user ${participant.id}:`, error);
                    });
                }, 1000 * Math.random()); // Stagger the requests
            }
        });
    }

    // Start preloading after initial load
    setTimeout(preloadMessages, 2000);
});

@extends('layouts.chatsLayout')
@section('title', 'Chats')
@section('css')
<link rel="stylesheet" href="{{ asset('css/chats.css') }}">
@endsection

@section('content')

<!-- Main Content -->
<main class="main-content">
    <!-- Chat Container -->
    <div class="chat-container">
        <!-- Chat List -->
        <div class="chat-list">
            @if($chatParticipants->isNotEmpty())
                @foreach($chatParticipants as $index => $participant)
                    <div class="chat-item {{ $index === 0 ? 'active' : '' }}" data-chat-id="{{ $participant['id'] }}">
                        <div class="chat-avatar">
                            <img src="{{ $participant['profile_photo'] }}" alt="{{ $participant['name'] }}" onerror="this.onerror=null;this.src='/assets/icons/default-avatar.svg';">
                            <span class="status-badge {{ $participant['status'] }}"></span>
                        </div>
                        <div class="chat-info">
                            <div class="chat-header">
                                <h3>{{ $participant['name'] }}</h3>
                                <span class="chat-time">{{ $participant['last_message_time'] }}</span>
                            </div>
                            <p class="chat-preview">{{ $participant['last_message'] }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-chats">
                    <p>No chat participants found.</p>
                    @if($user->role === 'doctor')
                        <p>Your patients will appear here once you have completed appointments.</p>
                    @else
                        <p>Your doctors will appear here once you have completed appointments.</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Chat Messages -->
        <div class="chat-messages">
            @if($chatParticipants->isNotEmpty())
                @php
                    $firstParticipant = $selectedChat ?: $chatParticipants->first();
                @endphp
                <div class="chat-header">
                    <div class="chat-user">
                        <img src="{{ $firstParticipant['profile_photo'] }}" alt="{{ $firstParticipant['name'] }}" onerror="this.onerror=null;this.src='/assets/icons/default-avatar.svg';">
                        <div class="user-info">
                            <h2>{{ $firstParticipant['name'] }}</h2>
                            <span class="status {{ $firstParticipant['status'] }}">{{ ucfirst($firstParticipant['status']) }}</span>
                        </div>
                    </div>
                    @if($user->role === 'doctor')
                        <div class="chat-actions">
                            <button class="call-btn video-call" title="Video Call"><i class="fas fa-video"></i></button>
                            <button class="call-btn audio-call" title="Audio Call"><i class="fas fa-phone"></i></button>
                        </div>
                    @endif
                </div>
            @else
                <div class="chat-header">
                    <div class="chat-user">
                        <img src="/assets/icons/default-avatar.svg" alt="No Chat Selected">
                        <div class="user-info">
                            <h2>No Chat Selected</h2>
                            <span class="status">Offline</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="messages-container" id="messagesContainer">
                @if($chatParticipants->isNotEmpty())
                    @if(!empty($messages))
                        @php
                            $currentDate = null;
                        @endphp
                        @foreach($messages as $message)
                            @php
                                $messageDate = \Carbon\Carbon::parse($message['timestamp'])->format('F j, Y');
                                $isCurrentUser = $message['sender_id'] == $user->id;
                                $messageTime = \Carbon\Carbon::parse($message['timestamp'])->format('g:i A');
                            @endphp
                            
                            @if($currentDate !== $messageDate)
                                <div class="message-date">{{ $messageDate }}</div>
                                @php $currentDate = $messageDate; @endphp
                            @endif
                            
                            <div class="message {{ $isCurrentUser ? 'sent' : 'received' }}" data-message-id="{{ $message['id'] }}">
                                @if(!$isCurrentUser)
                                    <div class="message-avatar">
                                        @php
                                            $participant = $selectedChat ?: $chatParticipants->first();
                                        @endphp
                                        <img src="{{ $participant['profile_photo'] }}" alt="{{ $participant['name'] }}" onerror="this.onerror=null;this.src='/assets/icons/default-avatar.svg';">
                                    </div>
                                @endif
                                <div class="message-content">
                                    <p>{{ $message['message'] }}</p>
                                    <span class="message-time">{{ $messageTime }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="message-date">{{ date('F j, Y') }}</div>
                        <div class="welcome-message">
                            <div class="message received">
                                @php
                                    $firstParticipant = $selectedChat ?: $chatParticipants->first();
                                @endphp
                                <div class="message-avatar">
                                    <img src="{{ $firstParticipant['profile_photo'] }}" alt="{{ $firstParticipant['name'] }}" onerror="this.onerror=null;this.src='/assets/icons/default-avatar.svg';">
                                </div>
                                <div class="message-content">
                                    <p>Hello! I'm ready to help you with your health concerns. How can I assist you today?</p>
                                    <span class="message-time">{{ date('g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="no-messages">
                        <p>Start a conversation by selecting a chat from the left sidebar.</p>
                    </div>
                @endif
            </div>

            <div class="chat-input">
                <button class="attach-btn">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="text" placeholder="Type a message..." maxlength="1000">
                <button class="send-btn" id="sendBtn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Chat Modal for Mobile -->
<div id="chatModal" class="chat-modal">
    <div class="chat-modal-content">
        <div class="chat-modal-header">
            <button class="chat-modal-back" id="closeChatModal">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <div class="chat-modal-user">
                <img id="modalUserAvatar" src="/assets/icons/default-avatar.svg" alt="User" onerror="this.onerror=null;this.src='/assets/icons/default-avatar.svg';">
                <div class="chat-modal-user-info">
                    <h3 id="modalUserName">Select a chat</h3>
                    <span class="status" id="modalUserStatus">Offline</span>
                </div>
            </div>
            <div class="chat-actions" id="modalChatActions" style="display: none;">
                @if($user->role === 'doctor')
                    <button class="call-btn video-call" title="Video Call"><i class="fas fa-video"></i></button>
                    <button class="call-btn audio-call" title="Audio Call"><i class="fas fa-phone"></i></button>
                @endif
            </div>
        </div>
        
        <div class="chat-modal-body">
            <div class="messages-container" id="modalMessagesContainer">
                <div class="no-messages">
                    <p>Start a conversation by selecting a chat from the left sidebar.</p>
                </div>
            </div>

            <div class="chat-input">
                <button class="attach-btn" id="modalAttachBtn">
                    <i class="fas fa-paperclip"></i>
                </button>
                <input type="text" id="modalChatInput" placeholder="Type a message..." maxlength="1000">
                <button class="send-btn" id="modalSendBtn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Call Overlay (hidden by default) -->
<div id="callOverlay" class="call-overlay hidden">
    <div id="remoteVideo" class="remote-video"></div>
    <div id="localVideo" class="local-video"></div>
    <div class="call-controls">
        <button id="toggleCamera" class="control-btn"><i class="fas fa-video"></i></button>
        <button id="toggleMute" class="control-btn"><i class="fas fa-microphone-slash"></i></button>
        <button id="flipCamera" class="control-btn"><i class="fas fa-sync-alt"></i></button>
        <button id="endCall" class="control-btn end"><i class="fas fa-phone-slash"></i></button>
    </div>
</div>

<!-- Incoming Call Modal -->
<div id="incomingCallModal" class="incoming-call-modal hidden">
    <div class="incoming-content">
        <img id="callerAvatar" class="caller-avatar" src="/assets/icons/default-avatar.svg" alt="Caller">
        <h2 id="incomingCallerName">Incoming Call</h2>
        <p id="incomingCallType">Video Call</p>
        <div class="incoming-buttons">
            <button id="declineCall" class="decline-btn"><i class="fas fa-phone-slash"></i> Decline</button>
            <button id="acceptCall" class="accept-btn"><i class="fas fa-phone"></i> Accept</button>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    // Pass data from PHP to JavaScript
    window.chatData = {
        currentUser: @json($user),
        chatParticipants: @json($chatParticipants),
        currentChatId: @json($currentChatId ?? null),
        selectedChat: @json($selectedChat ?? null),
        csrfToken: @json(csrf_token())
    };
    
    // Firebase configuration
    window.firebaseConfig = {
        databaseURL: "https://travel-care-5f873-default-rtdb.firebaseio.com"
    };
</script>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>

<script src="https://cdn.agora.io/sdk/release/AgoraRTC_N-4.19.0.js"></script>
<script src="{{ asset('js/chats.js') }}"></script>

<script src="{{ asset('js/call.js') }}"></script>

@endsection
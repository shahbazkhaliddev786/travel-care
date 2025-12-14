// Basic 1-to-1 Audio/Video calling over Agora + Firebase signalling
// Depends on: window.chatData, firebase (initialized by chats.js), AgoraRTC (cdn), and CSS markup added in chats.blade.php

(function () {
    // Wait until DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        // Ensure global dependencies are present
        if (!window.AgoraRTC) {
            console.error('AgoraRTC not found');
            return;
        }
        if (!window.firebase || !firebase.apps.length) {
            console.warn('firebase not initialised yet – call module will retry once initialised.');
            // Wait for Firebase to be initialized
            const checkFirebase = setInterval(() => {
                if (window.firebase && firebase.apps.length > 0) {
                    clearInterval(checkFirebase);
                    console.log('[Agora] Firebase now available, initializing call module');
                    initializeCallModule();
                }
            }, 100);
            return;
        }
        
        initializeCallModule();
    });
    
    function initializeCallModule() {

        const currentUser = window.chatData.currentUser;
        const csrfToken = window.chatData.csrfToken;
        const database = () => firebase.database(); // getter so that we always use initialised instance

        // DOM Elements
        const callOverlay = document.getElementById('callOverlay');
        const remoteVideoEl = document.getElementById('remoteVideo');
        const localVideoEl = document.getElementById('localVideo');
        const incomingModal = document.getElementById('incomingCallModal');
        const callerAvatarImg = document.getElementById('callerAvatar');
        const incomingCallerNameEl = document.getElementById('incomingCallerName');
        const incomingCallTypeEl = document.getElementById('incomingCallType');

        const acceptBtn = document.getElementById('acceptCall');
        const declineBtn = document.getElementById('declineCall');
        const endBtn = document.getElementById('endCall');
        const toggleCameraBtn = document.getElementById('toggleCamera');
        const toggleMuteBtn = document.getElementById('toggleMute');

        const videoCallBtnSelector = '.video-call';
        const audioCallBtnSelector = '.audio-call';

        let client = null;
        let localAudioTrack = null;
        let localVideoTrack = null;
        let remoteUid = null;
        let currentChannel = null;
        let currentCallType = 'video';
        let isMuted = false;
        let isCameraOff = false;
        let currentPartnerId = null;

        /* -----------------------------------------------------
         * Helper Functions
         * ---------------------------------------------------*/
        function getChatPartnerId() {
            const activeItem = document.querySelector('.chat-item.active');
            if (activeItem) {
                const partnerId = parseInt(activeItem.dataset.chatId);
                console.log('[Agora] Getting chat partner ID:', partnerId, 'from dataset:', activeItem.dataset.chatId);
                return partnerId;
            }
            console.warn('[Agora] No active chat item found');
            return null;
        }

        function generateChannelName(partnerId) {
            // Use chat id style for uniqueness
            const ids = [currentUser.id, partnerId].sort((a, b) => a - b);
            return `call_${ids[0]}_${ids[1]}`; // fixed channel between users
        }

        function showOverlay() {
            callOverlay.classList.remove('hidden');
        }

        function hideOverlay() {
            callOverlay.classList.add('hidden');
            // Clear video elements
            remoteVideoEl.innerHTML = '';
            localVideoEl.innerHTML = '';
        }

        function showIncomingModal() {
            incomingModal.classList.remove('hidden');
        }

        function hideIncomingModal() {
            incomingModal.classList.add('hidden');
        }

        async function fetchToken(channel) {
            const res = await fetch('/agora/token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ channel, uid: currentUser.id })
            });
            if (!res.ok) throw new Error('Failed to fetch token');
            return res.json();
        }

        function publishCallInvite(partnerId, type, channel) {
            console.log('[Agora] Publishing call invite', { partnerId, type, channel });
            const callRef = database().ref(`call_invites/${partnerId}/${channel}`);
            callRef.set({
                from: String(currentUser.id), // Ensure consistent string type
                type,
                channel,
                timestamp: Date.now(),
                status: 'calling'
            });
        }

        function removeCallInvite(partnerId, channel) {
            const callRef = database().ref(`call_invites/${partnerId}/${channel}`);
            callRef.remove();
        }

        /* -----------------------------------------------------
         * Agora Join / Leave
         * ---------------------------------------------------*/
        async function joinAgora(channelName, token, type) {
            if (client) {
                await leaveAgora();
            }
            client = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' });

            client.on('user-published', async (user, mediaType) => {
                await client.subscribe(user, mediaType);
                remoteUid = user.uid;
                if (mediaType === 'video') {
                    const track = user.videoTrack;
                    track.play(remoteVideoEl);
                }
                if (mediaType === 'audio') {
                    user.audioTrack.play(); // no container needed
                }
            });

            client.on('user-left', () => {
                endCurrentCall();
            });

            await client.join(window.chatData.agoraAppId || '', channelName, token, `${currentUser.id}`);

            localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
            if (type === 'video') {
                localVideoTrack = await AgoraRTC.createCameraVideoTrack();
                localVideoTrack.play(localVideoEl);
            }
            await client.publish([localAudioTrack, ...(localVideoTrack ? [localVideoTrack] : [])]);
        }

        async function leaveAgora() {
            try {
                if (localAudioTrack) {
                    localAudioTrack.stop();
                    localAudioTrack.close();
                    localAudioTrack = null;
                }
                if (localVideoTrack) {
                    localVideoTrack.stop();
                    localVideoTrack.close();
                    localVideoTrack = null;
                }
                if (client) {
                    await client.leave();
                    client.removeAllListeners();
                    client = null;
                }
            } catch (err) {
                console.error('Error leaving Agora:', err);
            }
        }

        /* -----------------------------------------------------
         * Call lifecycle handlers
         * ---------------------------------------------------*/
        async function startOutgoingCall(type) {
            const partnerId = getChatPartnerId();
            if (!partnerId) {
                alert('Please select a chat first');
                return;
            }
            console.log('[Agora] Starting outgoing call:', { type, partnerId, currentUserId: currentUser.id });
            currentCallType = type;
            currentPartnerId = partnerId;
            currentChannel = generateChannelName(partnerId);

            try {
                const { token, appId } = await fetchToken(currentChannel);
                window.chatData.agoraAppId = appId; // store globally for reuse
                await joinAgora(currentChannel, token, type);

                showOverlay();
                publishCallInvite(partnerId, type, currentChannel);
            } catch (err) {
                console.error('Failed to start call', err);
            }
        }

        async function answerIncomingCall(callData) {
            currentCallType = callData.type;
            currentPartnerId = parseInt(callData.from); // Ensure consistent number type
            currentChannel = callData.channel;
            remoteUid = callData.from;

            hideIncomingModal();
            showOverlay();

            try {
                const { token, appId } = await fetchToken(currentChannel);
                window.chatData.agoraAppId = appId;
                await joinAgora(currentChannel, token, currentCallType);

                // Update status to accepted
                const ownInviteRef = database().ref(`call_invites/${currentUser.id}/${currentChannel}`);
                ownInviteRef.update({ status: 'accepted' });
            } catch (err) {
                console.error('Error answering call:', err);
            }
        }

        async function endCurrentCall() {
            hideOverlay();
            await leaveAgora();
            if (currentChannel && currentPartnerId) {
                removeCallInvite(currentPartnerId, currentChannel);
            }
            currentChannel = null;
            currentPartnerId = null;
        }

        /* -----------------------------------------------------
         * UI Events
         * ---------------------------------------------------*/

        document.body.addEventListener('click', (e) => {
            // Video Call
            if (e.target.closest(videoCallBtnSelector)) {
                e.preventDefault();
                startOutgoingCall('video');
            }
            // Audio Call
            if (e.target.closest(audioCallBtnSelector)) {
                e.preventDefault();
                startOutgoingCall('audio');
            }
        });

        endBtn.addEventListener('click', endCurrentCall);

        toggleMuteBtn.addEventListener('click', () => {
            if (!localAudioTrack) return;
            isMuted = !isMuted;
            localAudioTrack.setEnabled(!isMuted);
            toggleMuteBtn.innerHTML = isMuted ? '<i class="fas fa-microphone"></i>' : '<i class="fas fa-microphone-slash"></i>';
        });

        toggleCameraBtn.addEventListener('click', () => {
            if (!localVideoTrack) return;
            isCameraOff = !isCameraOff;
            localVideoTrack.setEnabled(!isCameraOff);
            toggleCameraBtn.innerHTML = isCameraOff ? '<i class="fas fa-video-slash"></i>' : '<i class="fas fa-video"></i>';
        });

        acceptBtn.addEventListener('click', () => {
            if (incomingModal.dataset.callData) {
                const callData = JSON.parse(incomingModal.dataset.callData);
                answerIncomingCall(callData);
            }
        });

        declineBtn.addEventListener('click', () => {
            hideIncomingModal();
            if (incomingModal.dataset.callData) {
                const callData = JSON.parse(incomingModal.dataset.callData);
                const inviteRef = database().ref(`call_invites/${currentUser.id}/${callData.channel}`);
                inviteRef.update({ status: 'declined' });
            }
        });

        /* -----------------------------------------------------
         * Firebase listener for incoming invites
         * ---------------------------------------------------*/
        function setupInviteListener() {
            const invitesRef = database().ref(`call_invites/${currentUser.id}`);
            invitesRef.on('child_added', (snapshot) => {
                const data = snapshot.val();
                console.log('[Agora] Incoming call invite detected', snapshot.key, data);
                console.log('[Agora] Current user ID:', currentUser.id, 'Caller ID:', data.from);
                console.log('[Agora] Available chat participants:', window.chatData.chatParticipants);
                if (!data) return;
                if (data.status !== 'calling') return;
                // Show incoming modal
                const partner = window.chatData.chatParticipants.find(p => String(p.id) === String(data.from));
                console.log('[Agora] Found partner:', partner);
                if (partner) {
                    callerAvatarImg.src = partner.profile_photo || '/assets/icons/default-avatar.svg';
                    incomingCallerNameEl.textContent = partner.name;
                    incomingCallTypeEl.textContent = data.type === 'video' ? 'Video Call' : 'Audio Call';
                } else {
                    console.warn('[Agora] Partner not found in chat participants');
                }
                incomingModal.dataset.callData = JSON.stringify(data);
                showIncomingModal();
            });

            // Handle updates (declined / accepted / ended)
            invitesRef.on('child_changed', (snapshot) => {
                const data = snapshot.val();
                console.log('[Agora] Invite updated', snapshot.key, data);
                if (!data) return;
                if (data.status === 'calling') {
                    // Re-show modal if a new call comes on existing channel
                    const partner = window.chatData.chatParticipants.find(p => String(p.id) === String(data.from));
                    if (partner) {
                        callerAvatarImg.src = partner.profile_photo || '/assets/icons/default-avatar.svg';
                        incomingCallerNameEl.textContent = partner.name;
                        incomingCallTypeEl.textContent = data.type === 'video' ? 'Video Call' : 'Audio Call';
                    }
                    incomingModal.dataset.callData = JSON.stringify(data);
                    showIncomingModal();
                }
                else if (data.status === 'accepted') {
                    // Other party accepted; overlay already shown for caller
                }
                if (data.status === 'declined' || data.status === 'ended') {
                    hideIncomingModal();
                    endCurrentCall();
                }
            });
        }

        setupInviteListener();
    } // End of initializeCallModule
})();
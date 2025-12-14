<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Doctor;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChatsController extends Controller
{
    protected $firebaseService;
    
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    /**
     * Display the chats page with real doctor and customer data
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('signin');
        }

        // Set user online when they visit the chat page
        try {
            $this->firebaseService->setUserOnline($user->id);
        } catch (\Exception $e) {
            \Log::error('Failed to set user online: ' . $e->getMessage());
        }

        // Get chat participants based on user role
        $chatParticipants = $this->getChatParticipants($user);
        
        // Get the selected chat (if any)
        $selectedChatId = $request->query('chat_with');
        $selectedChat = null;
        $currentChatId = null;
        $messages = [];
        
        if ($selectedChatId) {
            $selectedChat = $this->getSelectedChat($user, $selectedChatId);
            if ($selectedChat) {
                // Generate chat ID and get messages
                $currentChatId = $this->firebaseService->generateChatId($user->id, $selectedChatId);
                $messages = $this->firebaseService->getMessages($currentChatId);
                
                // Ensure chat exists in Firebase
                $this->ensureChatExists($currentChatId, $user->id, $selectedChatId);
            }
        } elseif ($chatParticipants->isNotEmpty()) {
            // Default to first participant if no specific chat selected
            $firstParticipant = $chatParticipants->first();
            $currentChatId = $this->firebaseService->generateChatId($user->id, $firstParticipant['id']);
            $messages = $this->firebaseService->getMessages($currentChatId);
            
            // Ensure chat exists in Firebase
            $this->ensureChatExists($currentChatId, $user->id, $firstParticipant['id']);
        }

        return view('chats', compact('chatParticipants', 'selectedChat', 'user', 'currentChatId', 'messages'));
    }

    /**
     * Get chat participants based on user role
     */
    private function getChatParticipants($user)
    {
        $participants = collect();
        
        if ($user->role === 'doctor') {
            // For doctors, get patients they have appointments with
            $doctor = Doctor::where('user_id', $user->id)->first();
            
            if ($doctor) {
                $patientIds = Transaction::where('doctor_id', $doctor->id)
                    ->where('payment_status', 'completed')
                    ->pluck('user_id')
                    ->unique();
                
                $participants = User::whereIn('id', $patientIds)
                    ->select('id', 'name', 'profile_photo', 'role')
                    ->get()
                    ->map(function ($patient) {
                        // Get real-time status from Firebase
                        try {
                            $status = $this->firebaseService->getUserStatus($patient->id);
                        } catch (\Exception $e) {
                            \Log::error('Failed to get user status: ' . $e->getMessage());
                            $status = ['online' => false, 'typing' => false, 'last_seen' => null];
                        }
                        
                        // Ensure proper URL formatting for profile photo
                        $profilePhoto = $patient->profile_photo;
                        if ($profilePhoto && !str_starts_with($profilePhoto, 'http') && !str_starts_with($profilePhoto, '/')) {
                            $profilePhoto = '/' . $profilePhoto;
                        } elseif (!$profilePhoto) {
                            $profilePhoto = '/assets/icons/default-avatar.svg';
                        }
                        
                        return [
                            'id' => $patient->id,
                            'name' => $patient->name,
                            'profile_photo' => $profilePhoto,
                            'role' => $patient->role,
                            'status' => $status['online'] ? 'online' : 'offline',
                            'typing' => $status['typing'] ?? false,
                            'last_seen' => $status['last_seen'] ?? null,
                            'last_message' => 'Start a conversation...',
                            'last_message_time' => Carbon::now()->format('M j, g:i A')
                        ];
                    });
            }
        } else {
            // For customers, get doctors they have appointments with
            $doctorIds = Transaction::where('user_id', $user->id)
                ->where('payment_status', 'completed')
                ->pluck('doctor_id')
                ->unique();
            
            $participants = Doctor::whereIn('id', $doctorIds)
                ->with('user') // Load the associated user
                ->select('id', 'name', 'profile_image', 'user_id')
                ->get()
                ->map(function ($doctor) {
                    // Get real-time status from Firebase
                    try {
                        $status = $this->firebaseService->getUserStatus($doctor->user_id);
                    } catch (\Exception $e) {
                        \Log::error('Failed to get user status: ' . $e->getMessage());
                        $status = ['online' => false, 'typing' => false, 'last_seen' => null];
                    }
                    
                        // Ensure proper URL formatting for profile photo
                        $profilePhoto = $doctor->profile_image;
                        if ($profilePhoto && !str_starts_with($profilePhoto, 'http') && !str_starts_with($profilePhoto, '/')) {
                            $profilePhoto = '/' . $profilePhoto;
                        } elseif (!$profilePhoto) {
                            $profilePhoto = '/assets/icons/default-avatar.svg';
                        }
                        
                        return [
                            'id' => $doctor->user_id, // Use doctor's USER ID, not doctor record ID
                            'doctor_id' => $doctor->id, // Keep doctor record ID for reference
                            'name' => $doctor->name,
                            'profile_photo' => $profilePhoto,
                            'role' => 'doctor',
                            'status' => $status['online'] ? 'online' : 'offline',
                            'typing' => $status['typing'] ?? false,
                            'last_seen' => $status['last_seen'] ?? null,
                            'last_message' => 'Start a conversation...',
                            'last_message_time' => Carbon::now()->format('M j, g:i A')
                        ];
                });
        }
        
        return $participants;
    }

    /**
     * Get selected chat details
     */
    private function getSelectedChat($user, $chatId)
    {
        if ($user->role === 'doctor') {
            // For doctors, get patient details
            $patient = User::where('id', $chatId)
                ->select('id', 'name', 'profile_photo', 'role')
                ->first();
            
            if ($patient) {
                try {
                    $status = $this->firebaseService->getUserStatus($patient->id);
                } catch (\Exception $e) {
                    \Log::error('Failed to get user status: ' . $e->getMessage());
                    $status = ['online' => false, 'typing' => false, 'last_seen' => null];
                }
                
                // Ensure proper URL formatting for profile photo
                $profilePhoto = $patient->profile_photo;
                if ($profilePhoto && !str_starts_with($profilePhoto, 'http') && !str_starts_with($profilePhoto, '/')) {
                    $profilePhoto = '/' . $profilePhoto;
                } elseif (!$profilePhoto) {
                    $profilePhoto = '/assets/icons/default-avatar.svg';
                }
                
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'profile_photo' => $profilePhoto,
                    'role' => $patient->role,
                    'status' => $status['online'] ? 'online' : 'offline',
                    'typing' => $status['typing'] ?? false,
                    'last_seen' => $status['last_seen'] ?? null
                ];
            }
        } else {
            // For customers, get doctor details by USER ID
            $doctor = Doctor::where('user_id', $chatId)
                ->select('id', 'name', 'profile_image', 'user_id')
                ->first();
            
            if ($doctor) {
                try {
                    $status = $this->firebaseService->getUserStatus($doctor->user_id);
                } catch (\Exception $e) {
                    \Log::error('Failed to get user status: ' . $e->getMessage());
                    $status = ['online' => false, 'typing' => false, 'last_seen' => null];
                }
                
                // Ensure proper URL formatting for profile photo
                $profilePhoto = $doctor->profile_image;
                if ($profilePhoto && !str_starts_with($profilePhoto, 'http') && !str_starts_with($profilePhoto, '/')) {
                    $profilePhoto = '/' . $profilePhoto;
                } elseif (!$profilePhoto) {
                    $profilePhoto = '/assets/icons/default-avatar.svg';
                }
                
                return [
                    'id' => $doctor->user_id, // Use doctor's USER ID
                    'doctor_id' => $doctor->id, // Keep doctor record ID for reference
                    'name' => $doctor->name,
                    'profile_photo' => $profilePhoto,
                    'role' => 'doctor',
                    'status' => $status['online'] ? 'online' : 'offline',
                    'typing' => $status['typing'] ?? false,
                    'last_seen' => $status['last_seen'] ?? null
                ];
            }
        }
        
        return null;
    }

    /**
     * Get chat messages between two users
     */
    public function getMessages(Request $request)
    {
        $user = Auth::user();
        $chatWith = $request->query('chat_with');
        
        if (!$chatWith) {
            return response()->json(['error' => 'Chat participant not specified'], 400);
        }
        
        // Generate chat ID using user IDs (both participants must be user IDs)
        $chatId = $this->firebaseService->generateChatId($user->id, $chatWith);
        $messages = $this->firebaseService->getMessages($chatId);
        
        return response()->json([
            'messages' => $messages,
            'chat_id' => $chatId,
            'debug' => [
                'current_user_id' => $user->id,
                'chat_with_user_id' => $chatWith,
                'generated_chat_id' => $chatId
            ]
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'chat_with' => 'required|integer',
            'message' => 'required|string|max:1000',
            'type' => 'string|in:text,image,file|nullable'
        ]);
        
        // Generate chat ID using user IDs (both participants must be user IDs)
        $chatId = $this->firebaseService->generateChatId($user->id, $validated['chat_with']);
        
        // Ensure chat exists
        $this->ensureChatExists($chatId, $user->id, $validated['chat_with']);
        
        $messageData = [
            'sender_id' => $user->id,
            'sender_name' => $user->name,
            'message' => $validated['message'],
            'type' => $validated['type'] ?? 'text',
            'timestamp' => now()->toISOString(),
            'created_at' => now()->toISOString()
        ];
        
        $success = $this->firebaseService->sendMessage($chatId, $messageData);
        
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $messageData,
                'debug' => [
                    'chat_id' => $chatId,
                    'sender_id' => $user->id,
                    'chat_with' => $validated['chat_with']
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }
    
    /**
     * Ensure chat exists in Firebase
     */
    private function ensureChatExists(string $chatId, int $userId1, int $userId2)
    {
        $chatInfo = $this->firebaseService->getChatInfo($chatId);
        
        if (!$chatInfo) {
            // Create new chat
            $user1 = User::find($userId1);
            $user2 = User::find($userId2);
            
            if (!$user1 || !$user2) {
                return false;
            }
            
            $participants = [$userId1, $userId2];
            $chatInfo = [
                'participant_names' => [
                    $userId1 => $user1->name,
                    $userId2 => $user2->name
                ],
                'participant_photos' => [
                    $userId1 => $user1->profile_photo ?: '/assets/icons/default-avatar.svg',
                    $userId2 => $user2->profile_photo ?: '/assets/icons/default-avatar.svg'
                ]
            ];
            
            $this->firebaseService->createChat($chatId, $participants, $chatInfo);
        }
        
        return true;
    }
    
    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'chat_id' => 'required|string',
            'message_id' => 'required|string'
        ]);
        
        $success = $this->firebaseService->markAsRead(
            $validated['chat_id'],
            $user->id,
            $validated['message_id']
        );
        
        return response()->json([
            'success' => $success
        ]);
    }

    /**
     * Set user online status
     */
    public function setOnline(Request $request)
    {
        $user = Auth::user();
        $success = $this->firebaseService->setUserOnline($user->id);
        
        return response()->json(['success' => $success]);
    }

    /**
     * Set user offline status
     */
    public function setOffline(Request $request)
    {
        $user = Auth::user();
        $success = $this->firebaseService->setUserOffline($user->id);
        
        return response()->json(['success' => $success]);
    }

    /**
     * Set typing status
     */
    public function setTyping(Request $request)
    {
        $user = Auth::user();
        $typing = $request->input('typing', false);
        
        $success = $this->firebaseService->setTypingStatus($user->id, $typing);
        
        return response()->json(['success' => $success]);
    }

    /**
     * Get user status
     */
    public function getUserStatus(Request $request)
    {
        $userId = $request->input('user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'User ID is required'], 400);
        }
        
        $status = $this->firebaseService->getUserStatus($userId);
        
        return response()->json(['status' => $status]);
    }

    /**
     * Get multiple user statuses
     */
    public function getMultipleUserStatuses(Request $request)
    {
        $userIds = $request->input('user_ids', []);
        
        if (empty($userIds)) {
            return response()->json(['error' => 'User IDs are required'], 400);
        }
        
        $statuses = $this->firebaseService->getMultipleUserStatuses($userIds);
        
        return response()->json(['statuses' => $statuses]);
    }
} 
<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\DatabaseException;
use Kreait\Firebase\Exception\AuthException;
use Illuminate\Support\Facades\Log;
use Exception;

class FirebaseService
{
    protected $database;
    protected $auth;
    
    public function __construct()
    {
        try {
            $firebase = (new Factory)
                ->withServiceAccount(config_path('travel-care-5f873-firebase-adminsdk-fbsvc-7db7c34381.json'))
                ->withDatabaseUri('https://travel-care-5f873-default-rtdb.firebaseio.com');
            
            $this->database = $firebase->createDatabase();
            $this->auth = $firebase->createAuth();
        } catch (Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Send a message to Firebase Realtime Database
     *
     * @param string $chatId
     * @param array $messageData
     * @return bool
     */
    public function sendMessage(string $chatId, array $messageData): bool
    {
        try {
            $reference = $this->database->getReference('chats/' . $chatId . '/messages');
            $reference->push($messageData);
            
            // Also update the last message in the chat info
            $chatInfoRef = $this->database->getReference('chats/' . $chatId . '/info');
            $chatInfoRef->update([
                'last_message' => $messageData['message'],
                'last_message_time' => $messageData['timestamp'],
                'last_message_sender' => $messageData['sender_id']
            ]);
            
            return true;
        } catch (DatabaseException $e) {
            Log::error('Failed to send message to Firebase: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get messages for a specific chat
     *
     * @param string $chatId
     * @param int $limit
     * @return array
     */
    public function getMessages(string $chatId, int $limit = 50): array
    {
        try {
            $reference = $this->database->getReference('chats/' . $chatId . '/messages');
            $snapshot = $reference->orderByKey()->limitToLast($limit)->getSnapshot();
            
            $messages = [];
            foreach ($snapshot->getValue() ?: [] as $key => $message) {
                $messages[] = array_merge($message, ['id' => $key]);
            }
            
            return $messages;
        } catch (DatabaseException $e) {
            Log::error('Failed to get messages from Firebase: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create or update chat info
     *
     * @param string $chatId
     * @param array $participants
     * @param array $chatInfo
     * @return bool
     */
    public function createChat(string $chatId, array $participants, array $chatInfo = []): bool
    {
        try {
            $chatData = array_merge([
                'participants' => $participants,
                'created_at' => now()->toISOString(),
                'last_message' => '',
                'last_message_time' => now()->toISOString(),
                'last_message_sender' => null
            ], $chatInfo);
            
            $reference = $this->database->getReference('chats/' . $chatId . '/info');
            $reference->set($chatData);
            
            return true;
        } catch (DatabaseException $e) {
            Log::error('Failed to create chat in Firebase: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get chat info
     *
     * @param string $chatId
     * @return array|null
     */
    public function getChatInfo(string $chatId): ?array
    {
        try {
            $reference = $this->database->getReference('chats/' . $chatId . '/info');
            $snapshot = $reference->getSnapshot();
            
            return $snapshot->getValue();
        } catch (DatabaseException $e) {
            Log::error('Failed to get chat info from Firebase: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate a unique chat ID based on participants
     *
     * @param int $userId1
     * @param int $userId2
     * @return string
     */
    public function generateChatId(int $userId1, int $userId2): string
    {
        // Always put the smaller ID first to ensure consistency
        $ids = [$userId1, $userId2];
        sort($ids, SORT_NUMERIC); // Ensure numeric sorting
        $chatId = 'chat_' . implode('_', $ids);
        
        // Log for debugging
        \Log::info("Generated chat ID: {$chatId} for users {$userId1} and {$userId2}");
        
        return $chatId;
    }
    
    /**
     * Mark messages as read
     *
     * @param string $chatId
     * @param int $userId
     * @param string $messageId
     * @return bool
     */
    public function markAsRead(string $chatId, int $userId, string $messageId): bool
    {
        try {
            $reference = $this->database->getReference('chats/' . $chatId . '/read_status/' . $userId);
            $reference->set([
                'last_read_message' => $messageId,
                'last_read_time' => now()->toISOString()
            ]);
            
            return true;
        } catch (DatabaseException $e) {
            Log::error('Failed to mark message as read: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user's chats
     *
     * @param int $userId
     * @return array
     */
    public function getUserChats(int $userId): array
    {
        try {
            $reference = $this->database->getReference('chats');
            $snapshot = $reference->getSnapshot();
            
            $userChats = [];
            foreach ($snapshot->getValue() ?: [] as $chatId => $chatData) {
                if (isset($chatData['info']['participants']) && 
                    in_array($userId, $chatData['info']['participants'])) {
                    $userChats[$chatId] = $chatData['info'];
                }
            }
            
            return $userChats;
        } catch (DatabaseException $e) {
            Log::error('Failed to get user chats: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete a message
     *
     * @param string $chatId
     * @param string $messageId
     * @return bool
     */
    public function deleteMessage(string $chatId, string $messageId): bool
    {
        try {
            $reference = $this->database->getReference('chats/' . $chatId . '/messages/' . $messageId);
            $reference->remove();
            
            return true;
        } catch (DatabaseException $e) {
            Log::error('Failed to delete message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set user online status
     *
     * @param int $userId
     * @return bool
     */
    public function setUserOnline(int $userId): bool
    {
        try {
            $reference = $this->database->getReference("presence/{$userId}");
            $reference->set([
                'online' => true,
                'last_seen' => time(),
                'typing' => false
            ]);
            
            return true;
        } catch (DatabaseException $e) {
            Log::error('Failed to set user online: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set user offline status
     *
     * @param int $userId
     * @return bool
     */
    public function setUserOffline(int $userId): bool
    {
        try {
            $reference = $this->database->getReference("presence/{$userId}");
            $reference->set([
                'online' => false,
                'last_seen' => time(),
                'typing' => false
            ]);
            
            return true;
        } catch (DatabaseException $e) {
            Log::error('Failed to set user offline: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set typing status
     *
     * @param int $userId
     * @param bool $typing
     * @return bool
     */
    public function setTypingStatus(int $userId, bool $typing): bool
    {
        try {
            $reference = $this->database->getReference("presence/{$userId}/typing");
            $reference->set($typing);
            
            return true;
        } catch (DatabaseException $e) {
            Log::error('Failed to set typing status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user online status
     *
     * @param int $userId
     * @return array
     */
    public function getUserStatus(int $userId): array
    {
        try {
            $reference = $this->database->getReference("presence/{$userId}");
            $snapshot = $reference->getSnapshot();
            
            $data = $snapshot->getValue();
            
            if (!$data) {
                return [
                    'online' => false,
                    'last_seen' => null,
                    'typing' => false
                ];
            }
            
            return [
                'online' => $data['online'] ?? false,
                'last_seen' => $data['last_seen'] ?? null,
                'typing' => $data['typing'] ?? false
            ];
        } catch (DatabaseException $e) {
            Log::error('Failed to get user status: ' . $e->getMessage());
            return [
                'online' => false,
                'last_seen' => null,
                'typing' => false
            ];
        }
    }

    /**
     * Get multiple user statuses
     *
     * @param array $userIds
     * @return array
     */
    public function getMultipleUserStatuses(array $userIds): array
    {
        $statuses = [];
        
        foreach ($userIds as $userId) {
            $statuses[$userId] = $this->getUserStatus($userId);
        }
        
        return $statuses;
    }
} 
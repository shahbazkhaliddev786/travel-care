<?php

namespace App\Services;

use Twilio\Rest\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $sid;
    protected $token;
    protected $twilioNumber;
    protected $client;

    public function __construct()
    {
        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->twilioNumber = config('services.twilio.number');
        
        if ($this->sid && $this->token) {
            $this->client = new Client($this->sid, $this->token);
        }
    }

    /**
     * Send SMS OTP to the user's phone number
     *
     * @param string $to
     * @param string $otp
     * @return bool
     */
    public function sendOtp($to, $otp)
    {
        try {
            if (!$this->client) {
                Log::error('Twilio credentials not configured properly');
                return false;
            }
            
            $message = $this->client->messages->create(
                $to,
                [
                    'from' => $this->twilioNumber,
                    'body' => "Your TravelCare verification code is: $otp"
                ]
            );
            
            Log::info('OTP sent successfully: ' . $message->sid);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to send OTP: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the provided OTP matches the generated OTP
     *
     * @param string $userOtp
     * @param string $generatedOtp
     * @return bool
     */
    public function verifyOtp($userOtp, $generatedOtp)
    {
        // Ensure both values are strings and trimmed
        $userOtp = trim((string) $userOtp);
        $generatedOtp = trim((string) $generatedOtp);
        
        // Log comparison for debugging in development
        if (config('app.env') === 'local') {
            \Log::info('TwilioService OTP Comparison', [
                'user_otp' => $userOtp,
                'generated_otp' => $generatedOtp,
                'user_otp_length' => strlen($userOtp),
                'generated_otp_length' => strlen($generatedOtp),
                'match' => $userOtp === $generatedOtp
            ]);
        }
        
        return $userOtp === $generatedOtp;
    }
} 
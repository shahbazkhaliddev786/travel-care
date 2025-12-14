<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle incoming Stripe webhooks
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (!$signature) {
            Log::error('Missing Stripe signature header');
            return response('Missing signature', 400);
        }

        try {
            $result = $this->stripeService->handleWebhook($payload, $signature);
            
            return response()->json($result);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature: ' . $e->getMessage());
            return response('Invalid signature', 400);
        } catch (\Exception $e) {
            Log::error('Webhook handling failed: ' . $e->getMessage());
            return response('Webhook handling failed', 500);
        }
    }
} 
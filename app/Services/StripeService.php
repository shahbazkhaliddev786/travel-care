<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\SetupIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\User;
use App\Models\PaymentMethod as PaymentMethodModel;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create or retrieve a Stripe customer
     */
    public function createOrGetCustomer(User $user): Customer
    {
        try {
            // Check if user already has a Stripe customer ID
            if ($user->stripe_customer_id) {
                try {
                    return Customer::retrieve($user->stripe_customer_id);
                } catch (\Exception $e) {
                    // If customer doesn't exist on Stripe, create a new one
                    Log::warning("Stripe customer not found, creating new one: " . $e->getMessage());
                }
            }

            // Create new Stripe customer
            $customer = Customer::create([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'metadata' => [
                    'user_id' => $user->id,
                    'role' => $user->role ?? 'customer',
                ],
            ]);

            // Store Stripe customer ID in user record
            $user->update(['stripe_customer_id' => $customer->id]);

            return $customer;
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe customer: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a setup intent for adding payment methods
     */
    public function createSetupIntent(User $user): SetupIntent
    {
        try {
            $customer = $this->createOrGetCustomer($user);

            return SetupIntent::create([
                'customer' => $customer->id,
                'usage' => 'off_session',
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create setup intent: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a payment intent for processing payments
     */
    public function createPaymentIntent(User $user, int $amount, array $metadata = []): PaymentIntent
    {
        try {
            $customer = $this->createOrGetCustomer($user);

            return PaymentIntent::create([
                'amount' => $amount, // Amount in cents
                'currency' => 'usd',
                'customer' => $customer->id,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'metadata' => $metadata,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create payment intent: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Confirm a payment intent
     */
    public function confirmPaymentIntent(string $paymentIntentId, string $paymentMethodId): PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId)->confirm([
                'payment_method' => $paymentMethodId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to confirm payment intent: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve payment methods for a customer
     */
    public function getCustomerPaymentMethods(User $user): array
    {
        try {
            $customer = $this->createOrGetCustomer($user);

            $paymentMethods = PaymentMethod::all([
                'customer' => $customer->id,
                'type' => 'card',
            ]);

            return $paymentMethods->data;
        } catch (\Exception $e) {
            Log::error('Failed to get customer payment methods: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Attach a payment method to a customer
     */
    public function attachPaymentMethodToCustomer(string $paymentMethodId, User $user): PaymentMethod
    {
        try {
            $customer = $this->createOrGetCustomer($user);

            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $customer->id]);

            return $paymentMethod;
        } catch (\Exception $e) {
            Log::error('Failed to attach payment method: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Detach a payment method from a customer
     */
    public function detachPaymentMethod(string $paymentMethodId): PaymentMethod
    {
        try {
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            return $paymentMethod->detach();
        } catch (\Exception $e) {
            Log::error('Failed to detach payment method: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Save Stripe payment method to local database
     */
    public function savePaymentMethodToDatabase(PaymentMethod $stripePaymentMethod, User $user): PaymentMethodModel
    {
        try {
            $card = $stripePaymentMethod->card;
            
            return PaymentMethodModel::create([
                'user_id' => $user->id,
                'stripe_payment_method_id' => $stripePaymentMethod->id,
                'card_type' => $card->brand,
                'card_holder' => $user->name, // Default to user name
                'last_four' => $card->last4,
                'expiry_month' => str_pad($card->exp_month, 2, '0', STR_PAD_LEFT),
                'expiry_year' => substr($card->exp_year, -2),
                'name' => ucfirst($card->brand),
                'type' => $card->brand,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save payment method to database: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process a one-time payment
     */
    public function processPayment(User $user, string $paymentMethodId, int $amount, array $metadata = []): PaymentIntent
    {
        try {
            $customer = $this->createOrGetCustomer($user);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'customer' => $customer->id,
                'payment_method' => $paymentMethodId,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
                'metadata' => $metadata,
            ]);

            return $paymentIntent;
        } catch (\Exception $e) {
            Log::error('Failed to process payment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle Stripe webhooks
     */
    public function handleWebhook(string $payload, string $signature): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook.secret')
            );

            Log::info('Stripe webhook received: ' . $event->type);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;
                case 'payment_method.attached':
                    $this->handlePaymentMethodAttached($event->data->object);
                    break;
                case 'customer.created':
                    $this->handleCustomerCreated($event->data->object);
                    break;
                default:
                    Log::info('Unhandled webhook type: ' . $event->type);
            }

            return ['status' => 'success'];
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Webhook handling failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSucceeded($paymentIntent): void
    {
        Log::info('Payment succeeded for payment intent: ' . $paymentIntent->id);
        // Add your logic here for successful payments
        // e.g., update appointment status, send notifications, etc.
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed($paymentIntent): void
    {
        Log::warning('Payment failed for payment intent: ' . $paymentIntent->id);
        // Add your logic here for failed payments
        // e.g., notify user, retry payment, etc.
    }

    /**
     * Handle payment method attached
     */
    private function handlePaymentMethodAttached($paymentMethod): void
    {
        Log::info('Payment method attached: ' . $paymentMethod->id);
        // Add your logic here if needed
    }

    /**
     * Handle customer created
     */
    private function handleCustomerCreated($customer): void
    {
        Log::info('Stripe customer created: ' . $customer->id);
        // Add your logic here if needed
    }

    /**
     * Get publishable key for frontend
     */
    public function getPublishableKey(): string
    {
        return config('services.stripe.key');
    }

    /**
     * Calculate fee amount from dollar amount (convert to cents)
     */
    public function convertToCents(float $dollarAmount): int
    {
        return (int) round($dollarAmount * 100);
    }

    /**
     * Convert cents to dollar amount
     */
    public function convertToDollars(int $centsAmount): float
    {
        return $centsAmount / 100;
    }
} 
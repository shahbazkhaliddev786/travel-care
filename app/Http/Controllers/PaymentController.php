<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        
        // Get user's payment methods from database
        $paymentMethods = [];
        if ($user) {
            $paymentMethods = PaymentMethod::where('user_id', $user->id)->get();
        }
        
        // Get Stripe's publishable key for frontend
        $stripePublishableKey = $this->stripeService->getPublishableKey();
        
        // Default appointment overview (will be updated by JavaScript from sessionStorage)
        $appointmentOverview = [
            'doctor_name' => 'Dr. Martha Zoldana',
            'service_type' => 'House Visit',
            'date' => 'Mo, 17.08.2023',
            'time' => '8:00 AM — 8:30 AM',
            'location' => 'Jose Maria Morelos No.48, Montelon Centro, Mexico',
            'total_fee' => 300,
        ];
        
        return view('payment', compact('appointmentOverview', 'paymentMethods', 'stripePublishableKey'));
    }
    
    /**
     * Create payment intent for the appointment
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.50',
                'doctor_id' => 'nullable|exists:doctors,id',
                'service_type' => 'required|string',
                'appointment_date' => 'nullable|date',
                'appointment_time' => 'nullable|string',
                'location' => 'nullable|string',
            ]);

            $amountInCents = $this->stripeService->convertToCents($validated['amount']);
            
            $metadata = [
                'user_id' => $user->id,
                'service_type' => $validated['service_type'],
                'appointment_date' => $validated['appointment_date'] ?? null,
                'appointment_time' => $validated['appointment_time'] ?? null,
                'location' => $validated['location'] ?? null,
            ];

            if (isset($validated['doctor_id'])) {
                $metadata['doctor_id'] = $validated['doctor_id'];
            }

            $paymentIntent = $this->stripeService->createPaymentIntent($user, $amountInCents, $metadata);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create payment intent: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create payment intent',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create setup intent for adding payment methods
     */
    public function createSetupIntent(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $setupIntent = $this->stripeService->createSetupIntent($user);

            return response()->json([
                'success' => true,
                'client_secret' => $setupIntent->client_secret,
                'setup_intent_id' => $setupIntent->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create setup intent: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create setup intent',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment with existing payment method
     */
    public function processPayment(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Debug logging
            Log::info('Payment processing started', [
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'payment_method_id' => 'required|string',
                'amount' => 'required|numeric|min:0.50',
                'doctor_id' => 'nullable|exists:doctors,id',
                'service_type' => 'required|string',
                'appointment_date' => 'nullable|date',
                'appointment_time' => 'nullable|string',
                'location' => 'nullable|string',
                'save_payment_method' => 'boolean',
            ]);

            $amountInCents = $this->stripeService->convertToCents($validated['amount']);
            
            $metadata = [
                'user_id' => $user->id,
                'service_type' => $validated['service_type'],
                'appointment_date' => $validated['appointment_date'] ?? null,
                'appointment_time' => $validated['appointment_time'] ?? null,
                'location' => $validated['location'] ?? null,
            ];

            if (isset($validated['doctor_id'])) {
                $metadata['doctor_id'] = $validated['doctor_id'];
            }

            // Check if it's a saved payment method or a new one
            $paymentMethodId = $validated['payment_method_id'];
            
            // If it starts with 'pm_', it's a Stripe payment method ID
            if (strpos($paymentMethodId, 'pm_') === 0) {
                // Attach payment method to customer if it's not already attached
                $this->stripeService->attachPaymentMethodToCustomer($paymentMethodId, $user);
                
                // Save to database if requested
                if ($validated['save_payment_method'] ?? false) {
                    $stripePaymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
                    $this->stripeService->savePaymentMethodToDatabase($stripePaymentMethod, $user);
                }
            } elseif (strpos($paymentMethodId, 'card_') === 0) {
                // It's a saved payment method from our database with 'card_' prefix
                $databaseId = str_replace('card_', '', $paymentMethodId);
                
                // Debug logging
                Log::info('Looking for saved payment method', [
                    'original_id' => $paymentMethodId,
                    'database_id' => $databaseId,
                    'user_id' => $user->id
                ]);
                
                $savedPaymentMethod = PaymentMethod::where('user_id', $user->id)
                    ->where('id', $databaseId)
                    ->first();
                
                Log::info('Payment method query result', [
                    'found' => $savedPaymentMethod ? true : false,
                    'stripe_payment_method_id' => $savedPaymentMethod->stripe_payment_method_id ?? null
                ]);
                
                if (!$savedPaymentMethod || !$savedPaymentMethod->stripe_payment_method_id) {
                    Log::error('Payment method not found or missing Stripe ID', [
                        'database_id' => $databaseId,
                        'user_id' => $user->id,
                        'found' => $savedPaymentMethod ? true : false,
                        'has_stripe_id' => $savedPaymentMethod->stripe_payment_method_id ?? 'null'
                    ]);
                    return response()->json(['error' => 'Payment method not found or missing Stripe connection'], 404);
                }
                
                $paymentMethodId = $savedPaymentMethod->stripe_payment_method_id;
            } else {
                // Handle PayPal or other payment methods
                if ($paymentMethodId === 'paypal') {
                    return response()->json(['error' => 'PayPal integration coming soon'], 400);
                }
                
                // It's a direct database ID (legacy format)
                $savedPaymentMethod = PaymentMethod::where('user_id', $user->id)
                    ->where('id', $paymentMethodId)
                    ->first();
                
                if (!$savedPaymentMethod || !$savedPaymentMethod->stripe_payment_method_id) {
                    return response()->json(['error' => 'Payment method not found'], 404);
                }
                
                $paymentMethodId = $savedPaymentMethod->stripe_payment_method_id;
            }

            // Get doctor information for transaction record
            $doctor = null;
            $doctorName = 'Unknown Doctor';
            if (isset($validated['doctor_id'])) {
                $doctor = Doctor::find($validated['doctor_id']);
                $doctorName = $doctor ? $doctor->name : 'Unknown Doctor';
            }

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'doctor_id' => $validated['doctor_id'] ?? null,
                'payment_method_id' => $savedPaymentMethod->id ?? null,
                'amount' => $validated['amount'],
                'currency' => 'USD',
                'payment_status' => 'processing',
                'transaction_type' => 'appointment',
                'service_type' => $validated['service_type'],
                'doctor_name' => $doctorName,
                'appointment_date' => $validated['appointment_date'] ?? null,
                'appointment_time' => $validated['appointment_time'] ?? null,
                'location' => $validated['location'] ?? null,
                'metadata' => [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'payment_method_type' => strpos($validated['payment_method_id'], 'pm_') === 0 ? 'new_card' : 'saved_card',
                ]
            ]);

            // Process the payment
            $paymentIntent = $this->stripeService->processPayment($user, $paymentMethodId, $amountInCents, $metadata);

            // Update transaction with Stripe payment intent ID
            $transaction->stripe_payment_intent_id = $paymentIntent->id;
            $transaction->save();

            if ($paymentIntent->status === 'succeeded') {
                // Mark transaction as completed
                $transaction->markAsCompleted();
                
                return response()->json([
                    'success' => true,
                    'payment_intent' => $paymentIntent,
                    'transaction_id' => $transaction->transaction_id,
                    'transaction' => [
                        'id' => $transaction->transaction_id,
                        'amount' => $transaction->amount,
                        'doctor_name' => $transaction->doctor_name,
                        'service_type' => $transaction->service_type,
                        'appointment_date' => $transaction->appointment_date,
                        'appointment_time' => $transaction->appointment_time,
                        'location' => $transaction->location,
                        'paid_at' => $transaction->paid_at,
                    ],
                    'message' => 'Payment processed successfully'
                ]);
            } else {
                // Update transaction status
                $transaction->payment_status = $paymentIntent->status;
                $transaction->save();
                
                return response()->json([
                    'success' => false,
                    'status' => $paymentIntent->status,
                    'requires_action' => $paymentIntent->status === 'requires_action',
                    'client_secret' => $paymentIntent->client_secret,
                    'transaction_id' => $transaction->transaction_id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to process payment: ' . $e->getMessage());
            
            // Mark transaction as failed if it exists
            if (isset($transaction)) {
                $transaction->markAsFailed($e->getMessage());
            }
            
            return response()->json([
                'error' => 'Payment processing failed',
                'message' => $e->getMessage(),
                'transaction_id' => isset($transaction) ? $transaction->transaction_id : null,
            ], 500);
        }
    }

    /**
     * Confirm payment intent after 3D Secure or other authentication
     */
    public function confirmPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'payment_intent_id' => 'required|string',
            ]);

            $paymentIntent = \Stripe\PaymentIntent::retrieve($validated['payment_intent_id']);

            return response()->json([
                'success' => true,
                'payment_intent' => $paymentIntent,
                'status' => $paymentIntent->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to confirm payment: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to confirm payment',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment success (redirect after successful payment)
     */
    public function success(Request $request)
    {
        $paymentIntentId = $request->get('payment_intent');
        
        if ($paymentIntentId) {
            try {
                $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
                
                if ($paymentIntent->status === 'succeeded') {
                    return redirect()->route('home')->with('success', 'Payment processed successfully! Your appointment has been confirmed.');
                } else {
                    return redirect()->route('payment')->with('error', 'Payment was not completed. Please try again.');
                }
            } catch (\Exception $e) {
                Log::error('Failed to retrieve payment intent on success page: ' . $e->getMessage());
                return redirect()->route('payment')->with('error', 'Unable to verify payment status.');
            }
        }
        
        return redirect()->route('home')->with('success', 'Payment completed successfully!');
    }

    /**
     * Handle payment cancellation
     */
    public function cancel(Request $request)
    {
        return redirect()->route('payment')->with('error', 'Payment was cancelled. Please try again when you\'re ready.');
    }

    /**
     * Debug Stripe configuration and payment methods
     */
    public function debugStripe(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Get user's local payment methods
            $localPaymentMethods = PaymentMethod::where('user_id', $user->id)->get();
            
            // Test creating a customer
            $customer = $this->stripeService->createOrGetCustomer($user);
            
            // Test creating a setup intent
            $setupIntent = $this->stripeService->createSetupIntent($user);
            
            // Test creating a payment intent
            $paymentIntent = $this->stripeService->createPaymentIntent($user, 10000, ['test' => 'debug']);

            // Get Stripe payment methods
            $stripePaymentMethods = $this->stripeService->getCustomerPaymentMethods($user);

            return response()->json([
                'success' => true,
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'setup_intent_status' => $setupIntent->status,
                'payment_intent_status' => $paymentIntent->status,
                'payment_intent_client_secret' => $paymentIntent->client_secret,
                'local_payment_methods_count' => $localPaymentMethods->count(),
                'local_payment_methods' => $localPaymentMethods->map(function($pm) {
                    return [
                        'id' => $pm->id,
                        'card_type' => $pm->card_type,
                        'card_holder' => $pm->card_holder,
                        'last_four' => $pm->last_four,
                        'has_stripe_id' => !empty($pm->stripe_payment_method_id),
                        'stripe_payment_method_id' => $pm->stripe_payment_method_id
                    ];
                }),
                'stripe_payment_methods_count' => count($stripePaymentMethods),
                'stripe_payment_methods' => array_map(function($pm) {
                    return [
                        'id' => $pm->id,
                        'type' => $pm->type,
                        'card' => $pm->card ? [
                            'brand' => $pm->card->brand,
                            'last4' => $pm->card->last4,
                            'exp_month' => $pm->card->exp_month,
                            'exp_year' => $pm->card->exp_year,
                        ] : null
                    ];
                }, $stripePaymentMethods),
                'message' => 'Debug information retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe debug failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Stripe configuration error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get customer's Stripe payment methods
     */
    public function getPaymentMethods(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $stripePaymentMethods = $this->stripeService->getCustomerPaymentMethods($user);
            
            return response()->json([
                'success' => true,
                'payment_methods' => $stripePaymentMethods
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get payment methods: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve payment methods',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Migrate existing payment methods to Stripe
     */
    public function migratePaymentMethods(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Get payment methods without Stripe IDs
            $paymentMethodsToMigrate = PaymentMethod::where('user_id', $user->id)
                ->whereNull('stripe_payment_method_id')
                ->get();

            $results = [];
            
            foreach ($paymentMethodsToMigrate as $localMethod) {
                // Delete old payment methods that can't be migrated to Stripe
                // (Since we can't recreate Stripe payment methods from stored card numbers)
                $localMethod->delete();
                $results[] = [
                    'id' => $localMethod->id,
                    'action' => 'deleted',
                    'reason' => 'Cannot migrate to Stripe without original card details'
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment methods migration completed',
                'results' => $results,
                'recommendation' => 'Please add new payment methods using the Stripe Elements form'
            ]);

        } catch (\Exception $e) {
            Log::error('Payment methods migration failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Migration failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Legacy store method for backward compatibility
     */
    public function store(Request $request)
    {
        // Redirect to the new payment processing endpoint
        return $this->processPayment($request);
    }

    /**
     * List user's transactions
     */
    public function listTransactions(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('signin');
        }

        $transactions = Transaction::where('user_id', $user->id)
            ->with(['doctor', 'paymentMethod'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show specific transaction details
     */
    public function showTransaction(Request $request, string $transactionId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('signin');
        }

        $transaction = Transaction::where('transaction_id', $transactionId)
            ->where('user_id', $user->id)
            ->with(['doctor', 'paymentMethod'])
            ->first();

        if (!$transaction) {
            return redirect()->route('transactions.index')
                ->with('error', 'Transaction not found.');
        }

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show transaction receipt
     */
    public function showReceipt(Request $request, string $transactionId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('signin');
        }

        $transaction = Transaction::where('transaction_id', $transactionId)
            ->where('user_id', $user->id)
            ->with(['doctor', 'paymentMethod'])
            ->first();

        if (!$transaction) {
            return abort(404, 'Transaction not found.');
        }

        return view('transactions.receipt', compact('transaction'));
    }
} 
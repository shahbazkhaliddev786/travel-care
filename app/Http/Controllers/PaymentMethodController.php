<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Store a new payment method using Stripe
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user() ?? User::find(1); // Fallback for demo
            
            $validated = $request->validate([
                'payment_method_id' => 'required|string', // Stripe payment method ID
                'save_card' => 'boolean',
            ]);

            // Attach payment method to Stripe customer
            $stripePaymentMethod = $this->stripeService->attachPaymentMethodToCustomer(
                $validated['payment_method_id'], 
                $user
            );

            // Save to local database if requested
            if ($validated['save_card'] ?? true) {
                $localPaymentMethod = $this->stripeService->savePaymentMethodToDatabase($stripePaymentMethod, $user);
                
                return redirect()->route('profile')->with('success', 'Payment method added successfully!');
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment method attached to customer',
                'payment_method' => $stripePaymentMethod
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to store payment method: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Failed to add payment method',
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['payment_method' => 'Failed to add payment method: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Legacy method for storing cards with manual input (now deprecated in favor of Stripe Elements)
     */
    public function storeLegacy(Request $request)
    {
        $user = Auth::user() ?? User::find(1); // Fallback for demo
        
        $validated = $request->validate([
            'card_number' => 'required|string|min:16|max:19',
            'card_holder' => 'required|string|max:255',
            'expiry_month' => 'required|string|size:2',
            'expiry_year' => 'required|string|size:2',
            'cvv' => 'required|string|min:3|max:4',
        ]);
        
        // Additional custom validation
        $cardNumber = str_replace(' ', '', $validated['card_number']);
        
        // Validate card number contains only digits
        if (!ctype_digit($cardNumber)) {
            return redirect()->back()
                ->withErrors(['card_number' => 'Card number must contain only digits.'])
                ->withInput();
        }
        
        // Validate card holder contains only letters and spaces
        if (!preg_match('/^[a-zA-Z\s]+$/', $validated['card_holder'])) {
            return redirect()->back()
                ->withErrors(['card_holder' => 'Card holder name must contain only letters and spaces.'])
                ->withInput();
        }
        
        // Validate expiry month is numeric and in valid range
        if (!ctype_digit($validated['expiry_month'])) {
            return redirect()->back()
                ->withErrors(['expiry_month' => 'Expiry month must be numeric.'])
                ->withInput();
        }
        
        $expiryMonth = intval($validated['expiry_month']);
        if ($expiryMonth < 1 || $expiryMonth > 12) {
            return redirect()->back()
                ->withErrors(['expiry_month' => 'Expiry month must be between 01 and 12.'])
                ->withInput();
        }
        
        // Validate expiry year is numeric
        if (!ctype_digit($validated['expiry_year'])) {
            return redirect()->back()
                ->withErrors(['expiry_year' => 'Expiry year must be numeric.'])
                ->withInput();
        }
        
        // Validate CVV is numeric
        if (!ctype_digit($validated['cvv'])) {
            return redirect()->back()
                ->withErrors(['cvv' => 'CVV must be numeric.'])
                ->withInput();
        }
        
        // Validate expiry date is not in the past
        $currentYear = date('y');
        $currentMonth = date('m');
        $expiryYear = intval($validated['expiry_year']);
        
        if ($expiryYear < $currentYear || ($expiryYear == $currentYear && $expiryMonth < $currentMonth)) {
            return redirect()->back()
                ->withErrors(['expiry_date' => 'The expiry date cannot be in the past.'])
                ->withInput();
        }
        
        // Validate card number length
        if (strlen($cardNumber) < 16 || strlen($cardNumber) > 19) {
            return redirect()->back()
                ->withErrors(['card_number' => 'Card number must be between 16-19 digits.'])
                ->withInput();
        }
        
        // WARNING: This method stores card details locally (encrypted) but is not recommended
        // for production. Use Stripe Elements instead.
        PaymentMethod::create([
            'user_id' => $user->id,
            'card_type' => $this->detectCardType($cardNumber),
            'card_number' => $cardNumber, // This will be automatically encrypted
            'card_holder' => $validated['card_holder'],
            'last_four' => substr($cardNumber, -4),
            'expiry_month' => $validated['expiry_month'],
            'expiry_year' => $validated['expiry_year'],
        ]);
        
        return redirect()->route('profile')->with('success', 'Payment method added successfully!');
    }
    
    /**
     * Remove a payment method
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user() ?? User::find(1); // Fallback for demo
            $paymentMethod = PaymentMethod::findOrFail($id);
            
            // Check if payment method belongs to the user
            if ($paymentMethod->user_id !== $user->id) {
                return redirect()->route('profile')->with('error', 'You are not authorized to delete this payment method.');
            }

            // Detach from Stripe if it has a Stripe payment method ID
            if ($paymentMethod->stripe_payment_method_id) {
                try {
                    $this->stripeService->detachPaymentMethod($paymentMethod->stripe_payment_method_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to detach payment method from Stripe: ' . $e->getMessage());
                    // Continue with local deletion even if Stripe detachment fails
                }
            }

            // Delete from local database
            $paymentMethod->delete();
            
            return redirect()->route('profile')->with('success', 'Payment method deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete payment method: ' . $e->getMessage());
            return redirect()->route('profile')->with('error', 'Failed to delete payment method.');
        }
    }

    /**
     * Create a setup intent for adding new payment methods
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
     * Get user's payment methods
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Get local payment methods
            $localPaymentMethods = PaymentMethod::where('user_id', $user->id)->get();

            // Get Stripe payment methods
            $stripePaymentMethods = $this->stripeService->getCustomerPaymentMethods($user);

            return response()->json([
                'success' => true,
                'local_payment_methods' => $localPaymentMethods,
                'stripe_payment_methods' => $stripePaymentMethods
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
     * Set a payment method as default
     */
    public function setDefault(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $paymentMethod = PaymentMethod::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            // Update Stripe customer's default payment method
            if ($paymentMethod->stripe_payment_method_id) {
                $customer = $this->stripeService->createOrGetCustomer($user);
                
                \Stripe\Customer::update($customer->id, [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethod->stripe_payment_method_id,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Default payment method updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to set default payment method: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to set default payment method',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Detect card type from card number
     */
    private function detectCardType($cardNumber)
    {
        $firstDigit = substr($cardNumber, 0, 1);
        $firstTwoDigits = substr($cardNumber, 0, 2);
        
        if ($firstDigit == '4') {
            return 'visa';
        } elseif ($firstDigit == '5' || ($firstTwoDigits >= '51' && $firstTwoDigits <= '55')) {
            return 'mastercard';
        } elseif ($firstTwoDigits == '34' || $firstTwoDigits == '37') {
            return 'amex';
        } elseif ($firstTwoDigits == '60' || $firstTwoDigits == '65') {
            return 'discover';
        } else {
            return 'unknown';
        }
    }
}
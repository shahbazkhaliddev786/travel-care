<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'doctor_id',
        'payment_method_id',
        'stripe_payment_intent_id',
        'amount',
        'currency',
        'payment_status',
        'transaction_type',
        'service_type',
        'doctor_name',
        'appointment_date',
        'appointment_time',
        'location',
        'notes',
        'metadata',
        'paid_at',
        'failed_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'appointment_date' => 'date',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Generate unique transaction ID when creating a new transaction
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = self::generateUniqueTransactionId();
            }
        });
    }

    /**
     * Generate a unique transaction ID in format MX00000
     */
    public static function generateUniqueTransactionId()
    {
        $prefix = 'MX';
        $lastTransaction = self::where('transaction_id', 'LIKE', $prefix . '%')
            ->orderBy('transaction_id', 'desc')
            ->first();
        
        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->transaction_id, 2));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the doctor associated with the transaction.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the payment method used for the transaction.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope a query to only include failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Mark transaction as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'payment_status' => 'completed',
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'payment_status' => 'failed',
            'failed_at' => now(),
            'notes' => $reason ? $this->notes . ' | Failed: ' . $reason : $this->notes,
        ]);
    }

    /**
     * Mark transaction as refunded.
     */
    public function markAsRefunded($reason = null)
    {
        $this->update([
            'payment_status' => 'refunded',
            'refunded_at' => now(),
            'notes' => $reason ? $this->notes . ' | Refunded: ' . $reason : $this->notes,
        ]);
    }

    /**
     * Get formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'completed' => 'badge-success',
            'failed' => 'badge-danger',
            'pending' => 'badge-warning',
            'processing' => 'badge-info',
            'refunded' => 'badge-secondary',
            default => 'badge-light',
        };
    }

    /**
     * Get human-readable status.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->payment_status) {
            'completed' => 'Completed',
            'failed' => 'Failed',
            'pending' => 'Pending',
            'processing' => 'Processing',
            'refunded' => 'Refunded',
            default => 'Unknown',
        };
    }
}

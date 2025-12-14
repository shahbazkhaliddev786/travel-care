<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'country_code',
        'country',
        'city',
        'address',
        'languages',
        'specializations',
        'bio',
        'hourly_rate',
        'availability',
        'experience_years',

        'profile_image',
        'is_verified',
        'verification_date',
        'rejection_reason',
        'is_available',
        'rating',
        'total_jobs',
        'paypal_email',
        'bank_account_number',
        'bank_name',
        'bank_routing_number',
        'bank_account_holder_name',
    ];

    protected $casts = [
        'languages' => 'array',
        'specializations' => 'array',

        'availability' => 'array',
        'is_verified' => 'boolean',
        'is_available' => 'boolean',
        'verification_date' => 'datetime',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    /**
     * Get the user that owns the translator profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reviews for the translator.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get the payment methods for the translator.
     */
    public function paymentMethods()
    {
        return $this->morphToMany(PaymentMethod::class, 'paymentable');
    }

    /**
     * Get the average rating for the translator.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of reviews for the translator.
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Scope a query to only include verified translators.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include pending translators.
     */
    public function scopePending($query)
    {
        return $query->where('is_verified', false)->whereNull('rejection_reason');
    }

    /**
     * Scope a query to only include rejected translators.
     */
    public function scopeRejected($query)
    {
        return $query->where('is_verified', false)->whereNotNull('rejection_reason');
    }

    /**
     * Scope a query to only include available translators.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}
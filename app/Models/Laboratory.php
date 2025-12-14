<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'country_code',
        'license_number',
        'license_scan',
        'address',
        'city',
        'state',
        'country',
        'specialization',
        'bio',
        'consultation_fee',
        'messaging_fee',
        'video_call_fee',
        'house_visit_fee',
        'voice_call_fee',
        'working_hours_from',
        'working_hours_to',
        'working_days',
        'years_of_experience',
        'working_location',
        'profile_image',
        'gallery_images',
        'is_verified',
        'verification_date',
        'rejection_reason',
        'can_video_consult',
        'paypal_email',
        'bank_account_number',
        'bank_name',
        'bank_routing_number',
        'bank_account_holder_name',
    ];

    protected $casts = [
        'working_days' => 'array',
        'gallery_images' => 'array',
        'is_verified' => 'boolean',
        'can_video_consult' => 'boolean',
        'verification_date' => 'datetime',
        'consultation_fee' => 'decimal:2',
        'messaging_fee' => 'decimal:2',
        'video_call_fee' => 'decimal:2',
        'house_visit_fee' => 'decimal:2',
        'voice_call_fee' => 'decimal:2',
    ];

    /**
     * Get the user that owns the laboratory profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the services for the laboratory.
     */
    public function services()
    {
        return $this->hasMany(LabService::class, 'laboratory_id');
    }

    /**
     * Get the reviews for the laboratory.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get the payment methods for the laboratory.
     */
    public function paymentMethods()
    {
        // Use morphToMany to leverage the paymentables pivot table (payment_method_id, paymentable_type, paymentable_id)
        return $this->morphToMany(PaymentMethod::class, 'paymentable');
    }

    /**
     * Get the gallery images for the laboratory.
     */
    public function galleryImages()
    {
        // Polymorphic relation via gallery_images.imageable_type/imageable_id
        return $this->morphMany(GalleryImage::class, 'imageable');
    }

    /**
     * Get the average rating for the laboratory.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of reviews for the laboratory.
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Scope a query to only include verified laboratories.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include pending laboratories.
     */
    public function scopePending($query)
    {
        return $query->where('is_verified', false)->whereNull('rejection_reason');
    }

    /**
     * Scope a query to only include rejected laboratories.
     */
    public function scopeRejected($query)
    {
        return $query->where('is_verified', false)->whereNotNull('rejection_reason');
    }
}
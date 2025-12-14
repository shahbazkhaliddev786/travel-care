<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'country_code',
        'professional_id',
        'license_scan',
        'address',
        'specialization',
        'consultation_fee',
        'is_verified',
        'can_video_consult',
        'rejection_reason',
        'type',
        'profile_image',
        'messaging_fee',
        'video_call_fee',
        'house_visit_fee',
        'voice_call_fee',
        'working_hours_from',
        'working_hours_to',
        'working_days',
        'city',
        'years_of_experience',
        'working_location',
        'description',
        'payment_methods',
        'paypal_email',
        'gallery_images',
    ];
    
    protected $casts = [
        'working_days' => 'array',
        'payment_methods' => 'array',
        'gallery_images' => 'array',
        'is_verified' => 'boolean',
        'can_video_consult' => 'boolean',
    ];

    /**
     * Get the user that owns the doctor profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function services()
    {
        return $this->hasMany(Service::class, 'doctor_id');
    }
    
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
    
    /**
     * Get the transactions for this doctor.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
    
    public function getPatientsCountAttribute()
    {
        // To be implemented: count unique users from appointments
        return 0;
    }
    
    public function getRatingAttribute()
    {
        $reviewsCount = $this->reviews()->count();
        if ($reviewsCount == 0) {
            return '0.0';
        }
        
        $totalRating = $this->reviews()->sum('rating');
        return number_format($totalRating / $reviewsCount, 1);
    }

    /**
     * Scope to get only verified doctors
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get doctors with video consultation enabled
     */
    public function scopeVideoConsultationEnabled($query)
    {
        return $query->where('can_video_consult', true);
    }

    /**
     * Get full phone number with country code
     */
    public function getFullPhoneAttribute()
    {
        return $this->country_code . ' ' . $this->phone;
    }
}
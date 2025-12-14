<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'type',
        'email',
        'phone',
        'website',
        'logo',
        'description',
        'country',
        'city',
        'state',
        'postal_code',
        'address',
        'specialties',
        'facilities',
        'bed_count',
        'emergency_services',
        'pharmacy',
        'operating_hours_from',
        'operating_hours_to',
        'operating_days',
        'country_code',
        'professional_id',
        'profile_image',
        'license_scan',
        'is_verified',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_verified' => 'integer',
        'emergency_services' => 'boolean',
        'pharmacy' => 'boolean',
        'operating_days' => 'array',
        'bed_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the services associated with the hospital.
     * Note: This relationship has been disabled since hospital_id was removed from services.
     * Services are now only linked to doctors.
     * 
     * @return \Illuminate\Support\Collection
     */
    public function services()
    {
        // Return empty collection since services are no longer linked to hospitals
        return collect();
    }
    
    /**
     * Get the gallery images for the hospital.
     */
    public function galleryImages()
    {
        return $this->morphMany(GalleryImage::class, 'imageable');
    }
    
    /**
     * Get the seals of quality for the hospital.
     */
    public function sealsOfQuality()
    {
        return $this->morphMany(SealOfQuality::class, 'qualifiable');
    }
    
    /**
     * Get the payment methods for the hospital.
     */
    public function paymentMethods()
    {
        return $this->morphToMany(PaymentMethod::class, 'paymentable');
    }
    
    /**
     * Scope a query to only include hospitals with a specific verification status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('is_verified', $status);
    }
}
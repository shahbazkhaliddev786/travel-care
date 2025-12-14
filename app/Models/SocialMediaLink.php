<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'url',
        'icon_class',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Scope to get only active links.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Get formatted platform name.
     */
    public function getFormattedPlatformAttribute()
    {
        return ucfirst($this->platform);
    }

    /**
     * Get default icon class based on platform.
     */
    public function getDefaultIconClassAttribute()
    {
        $icons = [
            'facebook' => 'fab fa-facebook',
            'instagram' => 'fab fa-instagram',
            'linkedin' => 'fab fa-linkedin',
            'youtube' => 'fab fa-youtube',
            'twitter' => 'fab fa-twitter',
            'tiktok' => 'fab fa-tiktok',
            'whatsapp' => 'fab fa-whatsapp',
        ];

        return $icons[strtolower($this->platform)] ?? 'fab fa-globe';
    }

    /**
     * Get the icon class to use (custom or default).
     */
    public function getIconAttribute()
    {
        return $this->icon_class ?: $this->default_icon_class;
    }
}
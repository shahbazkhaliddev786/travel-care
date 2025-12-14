<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FooterPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'content',
        'is_active',
        'last_updated_by',
        'updated_by_user_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_updated_by' => 'datetime',
    ];

    /**
     * Get the user who last updated this page.
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    /**
     * Scope to get only active pages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get page by slug.
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Get formatted title for display.
     */
    public function getFormattedTitleAttribute()
    {
        return ucwords(str_replace('-', ' ', $this->title));
    }

    /**
     * Get the route name for this page.
     */
    public function getRouteNameAttribute()
    {
        // All pages now use the dynamic route
        return 'footer.dynamic';
    }
}
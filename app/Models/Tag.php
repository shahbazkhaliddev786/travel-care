<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Boot the model and auto-generate slug from name.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
                
                // Ensure slug is unique
                $originalSlug = $tag->slug;
                $counter = 1;
                while (static::where('slug', $tag->slug)->exists()) {
                    $tag->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
                
                // Ensure slug is unique
                $originalSlug = $tag->slug;
                $counter = 1;
                while (static::where('slug', $tag->slug)->where('id', '!=', $tag->id)->exists()) {
                    $tag->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });
    }
    
    /**
     * The services that belong to the tag.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_tag');
    }
}
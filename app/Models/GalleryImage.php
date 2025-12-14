<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'image_path',
        'title',
        'description',
        'imageable_id',
        'imageable_type',
    ];
    
    /**
     * Get the parent imageable model (doctor, professional, or hospital).
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
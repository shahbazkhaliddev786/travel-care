<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SealOfQuality extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'image_path',
        'issuing_authority',
        'issue_date',
        'expiry_date',
        'description',
        'qualifiable_id',
        'qualifiable_type',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];
    
    /**
     * Get the parent qualifiable model (professional or hospital).
     */
    public function qualifiable()
    {
        return $this->morphTo();
    }
}
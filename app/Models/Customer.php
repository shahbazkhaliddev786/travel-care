<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $table = 'customer_profiles';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'country',
        'city',
        'gender',
        'age',
        'weight',
        'chronic_pathologies',
        'allergies',
        'chronic_medications',
        'medical_info',
        'country_code',
        'phone_number',
        'verification_code',
        'is_verified',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified' => 'boolean',
        'age' => 'integer',
        'weight' => 'float',
    ];
    
    /**
     * Get the user that owns the customer profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

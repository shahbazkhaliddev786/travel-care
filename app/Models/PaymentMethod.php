<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'card_type',
        'card_number',
        'card_holder',
        'last_four',
        'expiry_month',
        'expiry_year',
        'name',
        'type',
        'stripe_payment_method_id',
    ];
    
    protected $casts = [
        'card_number' => 'encrypted',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getMaskedNumberAttribute()
    {
        return 'XXXX XXXX XXXX XXXX ' . $this->last_four;
    }
    
    /**
     * Get all of the hospitals that use this payment method.
     */
    public function hospitals()
    {
        return $this->morphedByMany(Hospital::class, 'paymentable');
    }
    
    /**
     * Get all of the doctors that use this payment method.
     */
    public function doctors()
    {
        return $this->morphedByMany(Doctor::class, 'paymentable');
    }
    
    /**
     * Get all of the laboratories that use this payment method.
     */
    public function laboratories()
    {
        return $this->morphedByMany(Laboratory::class, 'paymentable');
    }
    

}
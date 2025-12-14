<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'country_code',
        'phone_number',
        'password',
        'role',
        'profile_photo',
        'is_active',
        'stripe_customer_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }
    
    public function getChronicPathologiesArrayAttribute()
    {
        return json_decode($this->chronic_pathologies) ?? [];
    }
    
    public function getChronicMedicationArrayAttribute()
    {
        return json_decode($this->chronic_medication) ?? [];
    }
    
    /**
     * Get the customer profile associated with the user.
     */
    public function customerProfile()
    {
        return $this->hasOne(Customer::class);
    }
    
    /**
     * Get the transactions for this user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the doctor profile associated with the user.
     */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }
    
    /**
     * Get the laboratory profile associated with the user.
     */
    public function laboratory()
    {
        return $this->hasOne(Laboratory::class);
    }
    
    /**
     * Get the translator profile associated with the user.
     */
    public function translator()
    {
        return $this->hasOne(Translator::class);
    }
    
    /**
     * Check if the user is a doctor.
     */
    public function isDoctor()
    {
        return $this->role === 'doctor';
    }
    
    /**
     * Check if the user is a laboratory.
     */
    public function isLaboratory()
    {
        return $this->role === 'laboratory';
    }
    
    /**
     * Check if the user is a translator.
     */
    public function isTranslator()
    {
        return $this->role === 'translator';
    }
    
    /**
     * Check if the user is a customer.
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    
    protected $table = 'doctor_services';
    
    protected $fillable = [
        'doctor_id',
        'name',
        'description',
        'price',
        'duration',
    ];
    
    /**
     * Get the doctor that owns the service.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
    
    /**
     * The tags that belong to the service.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'service_tag');
    }
}
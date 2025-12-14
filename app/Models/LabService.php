<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabService extends Model
{
    use HasFactory;
    
    protected $table = 'lab_services';
    
    protected $fillable = [
        'laboratory_id',
        'name',
        'description',
        'price',
    ];
    
    /**
     * Get the laboratory that owns the service.
     */
    public function laboratory()
    {
        return $this->belongsTo(Laboratory::class, 'laboratory_id');
    }
    
    /**
     * The tags that belong to the service.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'lab_service_tag');
    }
}

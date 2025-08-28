<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalDetail extends Model
{
    protected $table      = 'medical_details';
    protected $primaryKey = 'medical_id';
    public $timestamps    = true;
    protected $guarded    = [];    // all fields mass-assignable

   protected $casts = [
    'medical_history'   => 'array',
  
    'created_at'        => 'datetime',
    'updated_at'        => 'datetime',
];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

     public function getAllergiesAttribute($value): array
    {
        // First decode: turns the stored string into a JSON string or array
        $decoded = json_decode($value, true);

        // If that first decode yielded a string, decode again
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        // Guarantee an array
        return is_array($decoded) ? $decoded : [];
    }
}
